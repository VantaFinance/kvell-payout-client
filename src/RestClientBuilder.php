<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout;

use Doctrine\Common\Annotations\AnnotationReader;
use Psr\Http\Client\ClientInterface as PsrHttpClient;
use Symfony\Component\PropertyInfo\Extractor\PhpStanExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorFromClassMetadata;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\UidNormalizer;
use Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Symfony\Component\Serializer\SerializerInterface as Serializer;
use Vanta\Integration\KvellPayout\Infrastructure\HttpClient\ConfigurationClient;
use Vanta\Integration\KvellPayout\Infrastructure\HttpClient\HttpClient;
use Vanta\Integration\KvellPayout\Infrastructure\HttpClient\Middleware\AuthorizationMiddleware;
use Vanta\Integration\KvellPayout\Infrastructure\HttpClient\Middleware\BadRequestMiddleware;
use Vanta\Integration\KvellPayout\Infrastructure\HttpClient\Middleware\ClientErrorMiddleware;
use Vanta\Integration\KvellPayout\Infrastructure\HttpClient\Middleware\InternalServerMiddleware;
use Vanta\Integration\KvellPayout\Infrastructure\HttpClient\Middleware\Middleware;
use Vanta\Integration\KvellPayout\Infrastructure\HttpClient\Middleware\PipelineMiddleware;
use Vanta\Integration\KvellPayout\Infrastructure\HttpClient\Middleware\SignRequestMiddleware;
use Vanta\Integration\KvellPayout\Infrastructure\HttpClient\Middleware\SignSslRequestMiddleware;
use Vanta\Integration\KvellPayout\Infrastructure\HttpClient\Middleware\UrlMiddleware;
use Vanta\Integration\KvellPayout\Infrastructure\Serializer\CardNormalizer;
use Vanta\Integration\KvellPayout\Infrastructure\Serializer\ErrorCodeDenormalizer;
use Vanta\Integration\KvellPayout\Infrastructure\Serializer\MoneyPositiveNormalizer;
use Vanta\Integration\KvellPayout\Infrastructure\Serializer\MoneyPositiveOrZeroNormalizer;
use Vanta\Integration\KvellPayout\Infrastructure\Serializer\PayoutDenormalizer;
use Vanta\Integration\KvellPayout\Infrastructure\Serializer\PhoneNumberNormalizer;
use Vanta\Integration\KvellPayout\Struct\SignKey;
use Vanta\Integration\KvellPayout\Transport\RestClientBank;
use Vanta\Integration\KvellPayout\Transport\RestClientPayoutClassic;
use Vanta\Integration\KvellPayout\Transport\RestClientPayoutSbp;

final class RestClientBuilder
{
    private const PROD_URL = 'https://api.pay.kvell.group';

    private const STAGE_URL = 'https://api.pay.stage.kvell.group';

    private PsrHttpClient $client;

    private Serializer $serializer;

    private SignKey $signKey;

    /**
     * @var non-empty-string
     */
    private string $url;

    /**
     * @var non-empty-string
     */
    private string $apiKey;

    /**
     * @var non-empty-string
     */
    private string $secretKey;

    /**
     * @var array<int, Middleware>
     */
    private array $middlewares;

    /**
     * @param array<int, Middleware> $middlewares
     * @param non-empty-string       $apiKey
     * @param non-empty-string       $secretKey
     * @param non-empty-string       $url
     */
    private function __construct(
        PsrHttpClient $client,
        Serializer $serializer,
        SignKey $signKey,
        string $apiKey,
        string $secretKey,
        string $url,
        array $middlewares
    ) {
        $this->url         = $url;
        $this->client      = $client;
        $this->apiKey      = $apiKey;
        $this->signKey     = $signKey;
        $this->secretKey   = $secretKey;
        $this->serializer  = $serializer;
        $this->middlewares = $middlewares;
    }

    /**
     * @psalm-suppress MixedArgumentTypeCoercion,TooManyArguments, UndefinedClass, MissingDependency, InvalidArgument
     *
     * @param non-empty-string $url
     * @param non-empty-string $apiKey
     * @param non-empty-string $secretKey
     */
    public static function create(PsrHttpClient $client, SignKey $signKey, string $apiKey, string $secretKey, string $url = self::PROD_URL): self
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $phpStanExtractor     = new PhpStanExtractor();

        $serializer = new SymfonySerializer([
            new UnwrappingDenormalizer(),
            new PayoutDenormalizer(),
            new CardNormalizer(),
            new ErrorCodeDenormalizer(),
            new BackedEnumNormalizer(),
            new PhoneNumberNormalizer(),
            new MoneyPositiveOrZeroNormalizer(),
            new MoneyPositiveNormalizer(),
            new DateTimeNormalizer(),
            new UidNormalizer(),
            new ObjectNormalizer(
                $classMetadataFactory,
                new MetadataAwareNameConverter($classMetadataFactory, new CamelCaseToSnakeCaseNameConverter()),
                null,
                new PropertyInfoExtractor(
                    [],
                    [$phpStanExtractor],
                    [],
                    [],
                    [],
                ),
                new ClassDiscriminatorFromClassMetadata($classMetadataFactory)
            ),
            new ArrayDenormalizer(),
        ], [new JsonEncoder()]);

        $middlewares = [
            new UrlMiddleware(),
            new AuthorizationMiddleware(),
            new SignRequestMiddleware(),
            new SignSslRequestMiddleware(),
            new ClientErrorMiddleware(),
            new InternalServerMiddleware(),
            new BadRequestMiddleware($serializer),
        ];

        return new self($client, $serializer, $signKey, $apiKey, $secretKey, $url, $middlewares);
    }

    public function addMiddleware(Middleware $middleware): self
    {
        return new self(
            $this->client,
            $this->serializer,
            $this->signKey,
            $this->apiKey,
            $this->secretKey,
            $this->url,
            array_merge($this->middlewares, [$middleware])
        );
    }

    /**
     * @param non-empty-array<int, Middleware> $middlewares
     */
    public function withMiddlewares(array $middlewares): self
    {
        return new self(
            $this->client,
            $this->serializer,
            $this->signKey,
            $this->apiKey,
            $this->secretKey,
            $this->url,
            $middlewares
        );
    }

    /**
     * @param non-empty-string $url
     */
    public function withUrl(string $url): self
    {
        return new self(
            $this->client,
            $this->serializer,
            $this->signKey,
            $this->apiKey,
            $this->secretKey,
            $url,
            $this->middlewares
        );
    }

    public function withEnvironmentProd(): self
    {
        return $this->withUrl(self::PROD_URL);
    }

    public function withEnvironmentStage(): self
    {
        return $this->withUrl(self::STAGE_URL);
    }

    public function withSerializer(Serializer $serializer): self
    {
        return new self(
            $this->client,
            $serializer,
            $this->signKey,
            $this->apiKey,
            $this->secretKey,
            $this->url,
            $this->middlewares
        );
    }

    public function withClient(PsrHttpClient $client): self
    {
        return new self(
            $client,
            $this->serializer,
            $this->signKey,
            $this->apiKey,
            $this->secretKey,
            $this->url,
            $this->middlewares
        );
    }

    public function createBankClient(): BankClient
    {
        return new RestClientBank(
            $this->serializer,
            new HttpClient(
                new ConfigurationClient($this->signKey, $this->apiKey, $this->secretKey, $this->url),
                new PipelineMiddleware($this->middlewares, $this->client)
            )
        );
    }

    public function createPayoutClassicClient(): PayoutClassicClient
    {
        return new RestClientPayoutClassic(
            $this->serializer,
            new HttpClient(
                new ConfigurationClient($this->signKey, $this->apiKey, $this->secretKey, $this->url),
                new PipelineMiddleware($this->middlewares, $this->client)
            )
        );
    }

    public function createPayoutSbpClient(): PayoutSbpClient
    {
        return new RestClientPayoutSbp(
            $this->serializer,
            new HttpClient(
                new ConfigurationClient($this->signKey, $this->apiKey, $this->secretKey, $this->url),
                new PipelineMiddleware($this->middlewares, $this->client)
            )
        );
    }
}
