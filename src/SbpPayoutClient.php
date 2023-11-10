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
use Vanta\Integration\KvellPayout\Request\PossibleToPay;
use Vanta\Integration\KvellPayout\Request\SbpPayout;
use Vanta\Integration\KvellPayout\Response\Bank;
use Vanta\Integration\KvellPayout\Response\Order;
use Vanta\Integration\KvellPayout\Response\PossibleToPayStatus;

interface SbpPayoutClient
{
    /**
     * @throws ClientException
     */
    public function createPayoutClassic(SbpPayout $request): Order;

    /**
     * @param non-empty-string $transactionId
     *
     * @throws ClientException
     */
    public function getPayout(string $transactionId): Order;

    /**
     * @return non-empty-string
     *
     * @throws ClientException
     */
    public function startCheckingPossibleToPay(PossibleToPay $request): string;

    /**
     * @param non-empty-string $requestId
     *
     * @throws ClientException
     */
    public function getStatusPossibleToPay(string $requestId): PossibleToPayStatus;

    /**
     * @return list<Bank>
     *
     * @throws ClientException
     */
    public function getBanks(): array;
}
