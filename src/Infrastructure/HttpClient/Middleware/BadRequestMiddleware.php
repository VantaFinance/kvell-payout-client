<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Infrastructure\HttpClient\Middleware;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;
use Symfony\Component\Serializer\SerializerInterface as Serializer;
use Vanta\Integration\KvellPayout\Infrastructure\HttpClient\ConfigurationClient;
use Vanta\Integration\KvellPayout\Infrastructure\HttpClient\Exception\BadRequestException;
use Vanta\Integration\KvellPayout\Response\Error;
use Yiisoft\Http\Status;

final readonly class BadRequestMiddleware implements Middleware
{
    public function __construct(
        private Serializer $serializer,
    ) {
    }

    public function process(Request $request, ConfigurationClient $configuration, callable $next): Response
    {
        /** @var Response $response */
        $response = $next($request, $configuration);

        if (!in_array($response->getStatusCode(), [Status::BAD_REQUEST, Status::UNPROCESSABLE_ENTITY])) {
            return $response;
        }

        try {
            $errors = $this->serializer->deserialize($response->getBody()->__toString(), Error::class . '[]', 'json', [
                UnwrappingDenormalizer::UNWRAP_PATH => '[errors]',
            ]);
        } catch (\Throwable) {
            return $response;
        }

        throw BadRequestException::create($response, $request, $errors);
    }
}
