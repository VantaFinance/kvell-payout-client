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
use Vanta\Integration\KvellPayout\Request\ClassicPayout;
use Vanta\Integration\KvellPayout\Response\Order;
use Vanta\Integration\KvellPayout\Response\TransactionStatus;

interface PayoutClassicClient
{
    /**
     * @throws ClientException
     */
    public function createPayoutOtp(ClassicPayout $request): TransactionStatus;

    /**
     * @throws ClientException
     */
    public function createPayoutClassic(ClassicPayout $request): Order;

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
