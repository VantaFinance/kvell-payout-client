<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Response;

enum PossibleToPayStatus: string
{
    case SUCCESS    = 'success';
    case PROCESSING = 'processing';
    case ERROR      = 'error';
}
