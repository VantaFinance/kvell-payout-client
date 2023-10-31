<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Infrastructure\HttpClient\Exception;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Vanta\Integration\KvellPayout\Response\Error;

final class BadRequestException extends KvellPayoutException
{
    /**
     * @var list<Error>
     */
    private array $errors;

    /**
     * @param list<Error> $errors
     */
    public static function create(Response $response, Request $request, array $errors = []): self
    {
        $self         = new self($response, $request, 'Bad request');
        $self->errors = $errors;

        return $self;
    }

    /**
     * @return list<Error>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
