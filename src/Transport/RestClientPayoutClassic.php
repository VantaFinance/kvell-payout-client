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
use Vanta\Integration\KvellPayout\Infrastructure\HttpClient\HttpClient;
use Vanta\Integration\KvellPayout\PayoutClassicClient;
use Vanta\Integration\KvellPayout\Request\PayoutCard;
use Vanta\Integration\KvellPayout\Response\Order;
use Vanta\Integration\KvellPayout\Response\PayoutClassic;
use Vanta\Integration\KvellPayout\Response\PayoutOtp;
use Yiisoft\Http\Method;

final readonly class RestClientPayoutClassic implements PayoutClassicClient
{
    public function __construct(
        private Serializer $serializer,
        private HttpClient $httpClient,
    ) {
    }

    public function createPayoutOtp(PayoutCard $request): PayoutOtp
    {
        return $this->createPayout($request, PayoutOtp::class);
    }

    public function createPayoutClassic(PayoutCard $request): PayoutClassic
    {
        return $this->createPayout($request, PayoutClassic::class);
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
    private function createPayout(PayoutCard $request, string $type)
    {
        $content  = $this->serializer->serialize($request, 'json');
        $request  = new Request(Method::POST, '/v1/orders/account2card', ['ssl-sign' => 'ssl'], $content);
        $response = $this->httpClient->sendRequest($request);

        /** @var T|null $result */
        $result = $this->serializer->deserialize($response->getBody()->__toString(), $type, 'json');

        if (null == $result) {
            throw new \RuntimeException(sprintf('Not supported operation: %s', $type));
        }

        return $result;
    }
}
