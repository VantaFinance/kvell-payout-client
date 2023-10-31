<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Request;

final readonly class Receipt
{
    /**
     * @param non-empty-list<OrderItem> $items
     * @param non-empty-string          $group
     */
    public function __construct(
        public Company $company,
        public Client $client,
        public array $items,
        public string $group = 'Main'
    ) {
    }
}
