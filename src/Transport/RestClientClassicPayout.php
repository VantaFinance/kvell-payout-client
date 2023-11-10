<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Transport;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface as Serializer;
use Vanta\Integration\KvellPayout\ClassicPayoutClient;
use Vanta\Integration\KvellPayout\Infrastructure\HttpClient\HttpClient;
use Vanta\Integration\KvellPayout\Infrastructure\Serializer\PayoutDenormalizer;
use Vanta\Integration\KvellPayout\Request\ClassicPayout;
use Vanta\Integration\KvellPayout\Response\Order;
use Vanta\Integration\KvellPayout\Response\TransactionStatus;
use Yiisoft\Http\Method;

final readonly class RestClientClassicPayout implements ClassicPayoutClient
{
    public function __construct(
        private Serializer $serializer,
        private HttpClient $httpClient,
    ) {
    }

    public function createPayoutOtp(ClassicPayout $request): TransactionStatus
    {
        return $this->createPayout($request, TransactionStatus::class);
    }

    public function createPayoutClassic(ClassicPayout $request): Order
    {
        return $this->createPayout($request, Order::class);
    }

    public function approvePayout(string $transactionId, string $otp): Order
    {
        $requestContent = $this->serializer->serialize(['transaction' => $transactionId, 'otp' => $otp], 'json');
        $request        = new Request(Method::POST, '/v1/orders/account2card/confirm', [], $requestContent);
        $content        = $this->httpClient->sendRequest($request)->getBody()->__toString();

        return $this->serializer->deserialize($content, Order::class, 'json');
    }

    public function getPayout(string $transactionId): Order
    {
        $request = new Request(Method::GET, sprintf('/v1/orders/%s', $transactionId), ['digest' => $transactionId]);
        $content = $this->httpClient->sendRequest($request)->getBody()->__toString();

        return $this->serializer->deserialize($content, Order::class, 'json');
    }

    /**
     * @template T
     *
     * @param class-string<T> $type
     *
     * @return T
     *
     * @throws ClientExceptionInterface
     */
    private function createPayout(ClassicPayout $request, string $type)
    {
        $content  = $this->serializer->serialize($request, 'json');
        $request  = new Request(Method::POST, '/v1/orders/account2card', ['ssl-sign' => 'ssl'], $content);
        $response = $this->httpClient->sendRequest($request);

        /** @var T|null $result */
        $result = $this->serializer->deserialize($response->getBody()->__toString(), $type, 'json', [
            PayoutDenormalizer::TRANSFORM => true,
        ]);

        if (null == $result) {
            throw new \RuntimeException(sprintf('Not supported operation: %s', $type));
        }

        return $result;
    }
}
