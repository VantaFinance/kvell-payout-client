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
use Vanta\Integration\KvellPayout\Infrastructure\HttpClient\ConfigurationClient;

final readonly class SignRequestMiddleware implements Middleware
{
    public function process(Request $request, ConfigurationClient $configuration, callable $next): Response
    {
        $currentDigest = $request->getHeaderLine('digest');
        $request       = $request->withoutHeader('digest');

        if ('' == $currentDigest) {
            return $next($request, $configuration);
        }

        $digest    = $configuration->apiKey . $currentDigest . $configuration->secretKey;
        $signature = hash('sha256', $digest);

        return $next($request->withAddedHeader('X-Signature', $signature), $configuration);
    }
}
