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

final class SignSslRequestMiddleware implements Middleware
{
    private ?\OpenSSLAsymmetricKey $signKey;

    public function __construct()
    {
        $this->signKey = null;
    }

    public function process(Request $request, ConfigurationClient $configuration, callable $next): Response
    {
        $isSignRequest = $request->hasHeader('ssl-sign');
        $request       = $request->withoutHeader('ssl-sign');

        if (!$isSignRequest) {
            return $next($request, $configuration);
        }

        $this->signKey ??= $configuration->signKey->toOpensslPrivateKey();

        $digest    = $request->getBody()->__toString() . $configuration->secretKey;
        $signature = '';

        if (!openssl_sign($digest, $signature, $this->signKey, 'sha256WithRSAEncryption')) {
            throw new \RuntimeException(sprintf('Failed sign request: %s', $request->getUri()->__toString()));
        }

        return $next($request->withAddedHeader('X-Signature', base64_encode($signature)), $configuration);
    }
}
