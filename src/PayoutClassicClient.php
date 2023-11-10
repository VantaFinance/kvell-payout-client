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
use Vanta\Integration\KvellPayout\Request\PayoutCard;
use Vanta\Integration\KvellPayout\Response\Order;
use Vanta\Integration\KvellPayout\Response\PayoutClassic;
use Vanta\Integration\KvellPayout\Response\PayoutOtp;

interface PayoutClassicClient
{
    /**
     * @throws ClientException
     */
    public function createPayoutOtp(PayoutCard $request): PayoutOtp;

    /**
     * @throws ClientException
     */
    public function createPayoutClassic(PayoutCard $request): PayoutClassic;

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
    public function getPayout(string $transactionId): Order;
}
