<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Response;

final readonly class Error
{
    /**
     * @param non-empty-string $message
     */
    public function __construct(
        public ErrorCode $code,
        public string $message,
    ) {
    }
}
