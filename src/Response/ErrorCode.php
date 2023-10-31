<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Response;

enum ErrorCode: int
{
    case UNKNOWN_ERROR                     = 20000;
    case INVALID_API_KEY                   = 20001;
    case INVALID_SIGNATURE                 = 20002;
    case PSP_PROFILE_NOT_LINKED_TO_STORE   = 20003;
    case SESSION_NOT_FOUND                 = 20004;
    case ERROR_FROM_PSP_SERVICE            = 20005;
    case STORE_NOT_FOUND                   = 20006;
    case TRANSACTION_BEEN_COMPLETED_BEFORE = 20007;
    case UNPROCESSED_STATUS_TRANSACTION    = 20008;
    case PAYOUT_LIMIT_EXCEEDED             = 20019;

    public static function fromCode(int $value): ErrorCode
    {
        return self::tryFrom($value) ?? ErrorCode::UNKNOWN_ERROR;
    }
}
