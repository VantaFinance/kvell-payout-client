<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Infrastructure\HttpClient\Exception;

use Psr\Http\Client\ClientExceptionInterface as ClientException;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

abstract class KvellPayoutException extends \Exception implements ClientException
{
    private Response $response;

    private Request $request;

    final protected function __construct(
        Response $response,
        Request $request,
        string $message = '',
        int $code = 0,
        \Throwable $previous = null
    ) {
        $this->response = $response;
        $this->request  = $request;

        parent::__construct($message, $code, $previous);
    }

    final public function getResponse(): Response
    {
        return $this->response;
    }

    final public function getRequest(): Request
    {
        return $this->request;
    }
}
