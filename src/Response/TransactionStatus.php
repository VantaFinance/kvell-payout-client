<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Response;

enum TransactionStatus: string
{
    case NEW          = 'new';
    case PROCESSING   = 'processing';
    case CANCELED     = 'canceled';
    case COMPLETED    = 'completed';
    case WAIT_CONFIRM = 'wait_confirm';
}
