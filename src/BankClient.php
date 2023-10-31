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
use Vanta\Integration\KvellPayout\Response\Bank;

interface BankClient
{
    /**
     * @return list<Bank>
     *
     * @throws ClientException
     */
    public function getBanks(): array;
}
