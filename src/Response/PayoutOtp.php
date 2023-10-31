<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Response;

final readonly class PayoutOtp
{
    /**
     * @param non-empty-string $status
     */
    public function __construct(
        public string $status,
    ) {
    }
}
