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
use Vanta\Integration\KvellPayout\Infrastructure\HttpClient\Exception\BadRequestException;
use Vanta\Integration\KvellPayout\Infrastructure\HttpClient\Exception\ForbiddenException;
use Vanta\Integration\KvellPayout\Infrastructure\HttpClient\Exception\NotFoundException;
use Vanta\Integration\KvellPayout\Infrastructure\HttpClient\Exception\UnauthorizedException;
use Yiisoft\Http\Status;

final readonly class ClientErrorMiddleware implements Middleware
{
    public function process(Request $request, ConfigurationClient $configuration, callable $next): Response
    {
        $response   = $next($request, $configuration);
        $statusCode = $response->getStatusCode();

        if (Status::UNAUTHORIZED == $statusCode) {
            throw UnauthorizedException::create($response, $request);
        }

        if (Status::FORBIDDEN == $statusCode) {
            throw ForbiddenException::create($response, $request);
        }

        if (Status::NOT_FOUND === $statusCode) {
            throw NotFoundException::create($response, $request);
        }

        if ($statusCode >= Status::BAD_REQUEST && $statusCode <= Status::UNAVAILABLE_FOR_LEGAL_REASONS) {
            throw BadRequestException::create($response, $request);
        }

        return $response;
    }
}
