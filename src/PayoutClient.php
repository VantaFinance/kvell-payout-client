<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout;

use Psr\Http\Client\ClientExceptionInterface as ClientException;
use Vanta\Integration\KvellPayout\Request\Payout;
use Vanta\Integration\KvellPayout\Request\PossibleToPay;
use Vanta\Integration\KvellPayout\Response\Order;
use Vanta\Integration\KvellPayout\Response\PayoutClassic;
use Vanta\Integration\KvellPayout\Response\PayoutOtp;
use Vanta\Integration\KvellPayout\Response\PossibleToPayStatus;

interface PayoutClient
{
    /**
     * @return non-empty-string
     *
     * @throws ClientException
     */
    public function startCheckingPossibleToPay(PossibleToPay $request): string;

    /**
     * @param non-empty-string $requestId*
     *
     * @throws ClientException
     */
    public function getStatusPossibleToPay(string $requestId): PossibleToPayStatus;

    /**
     * @throws ClientException
     */
    public function createPayoutOtp(Payout $request): PayoutOtp;

    /**
     * @throws ClientException
     */
    public function createPayoutClassic(Payout $request): PayoutClassic;

    /**
     * @param non-empty-string $otp
     * @param non-empty-string $transactionId
     *
     * @throws ClientException
     */
    public function approvePayout(string $transactionId, string $otp): Order;

    /**
     * @param non-empty-string $transactionId
     *
     * @throws ClientException
     */
    public function getOrder(string $transactionId): Order;
}
