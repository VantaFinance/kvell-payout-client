<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Infrastructure\HttpClient\Middleware;

use Psr\Http\Client\ClientExceptionInterface as ClientException;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Vanta\Integration\KvellPayout\Infrastructure\HttpClient\ConfigurationClient;

interface Middleware
{
    /**
     * @param callable(Request, ConfigurationClient): Response $next
     *
     * @throws ClientException
     */
    public function process(Request $request, ConfigurationClient $configuration, callable $next): Response;
}
