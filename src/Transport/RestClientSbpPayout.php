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
use Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;
use Symfony\Component\Serializer\SerializerInterface as Serializer;
use Vanta\Integration\KvellPayout\Infrastructure\HttpClient\HttpClient;
use Vanta\Integration\KvellPayout\Request\PossibleToPay;
use Vanta\Integration\KvellPayout\Request\SbpPayout;
use Vanta\Integration\KvellPayout\Response\Bank;
use Vanta\Integration\KvellPayout\Response\Order;
use Vanta\Integration\KvellPayout\Response\PossibleToPayStatus;
use Vanta\Integration\KvellPayout\SbpPayoutClient;
use Yiisoft\Http\Method;

final readonly class RestClientSbpPayout implements SbpPayoutClient
{
    public function __construct(
        private Serializer $serializer,
        private HttpClient $httpClient,
    ) {
    }

    public function startCheckingPossibleToPay(PossibleToPay $request): string
    {
        $requestContent = $this->serializer->serialize($request, 'json');
        $request        = new Request(Method::POST, '/v1/orders/payout/sbp/check', [
            'digest' => str_replace('+', '', $request->phoneNumber->jsonSerialize()) . $request->bankId,
        ], $requestContent);
        $content = $this->httpClient->sendRequest($request)->getBody()->__toString();

        /**@phpstan-ignore-next-line**/
        return $this->serializer->deserialize($content, 'string', 'json', [
            UnwrappingDenormalizer::UNWRAP_PATH => '[request_id]',
        ]);
    }

    public function getStatusPossibleToPay(string $requestId): PossibleToPayStatus
    {
        $request = new Request(Method::GET, sprintf('/v1/orders/payout/sbp/check/status/%s', $requestId), ['digest' => $requestId]);
        $content = $this->httpClient->sendRequest($request)->getBody()->__toString();

        return $this->serializer->deserialize($content, PossibleToPayStatus::class, 'json', [
            UnwrappingDenormalizer::UNWRAP_PATH => '[status]',
        ]);
    }

    public function createPayoutClassic(SbpPayout $request): Order
    {
        $content  = $this->serializer->serialize($request, 'json');
        $request  = new Request(Method::POST, '/v1/orders/payout/sbp', ['ssl-sign' => 'ssl'], $content);
        $response = $this->httpClient->sendRequest($request);

        return $this->serializer->deserialize($response->getBody()->__toString(), Order::class, 'json');
    }

    public function getPayout(string $transactionId): Order
    {
        $request = new Request(Method::GET, sprintf('/v1/orders/%s', $transactionId), ['digest' => $transactionId]);
        $content = $this->httpClient->sendRequest($request)->getBody()->__toString();

        return $this->serializer->deserialize($content, Order::class, 'json');
    }

    public function getBanks(): array
    {
        $request = new Request(Method::GET, '/v1/collections/banks');
        $content = $this->httpClient->sendRequest($request)->getBody()->__toString();

        return $this->serializer->deserialize($content, Bank::class . '[]', 'json');
    }
}
