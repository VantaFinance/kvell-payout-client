<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Request;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Vanta\Integration\KvellPayout\Struct\MoneyPositive;

final readonly class Payout
{
    /**
     * @param non-empty-string          $description
     * @param non-empty-string          $transactionId
     * @param non-empty-string|null     $customer
     * @param array<string, mixed>|null $extraData
     */
    public function __construct(
        public string $description,
        #[SerializedName('transaction')]
        public string $transactionId,
        public MoneyPositive $amount,
        #[SerializedName('recipient_pan')]
        public Card $card,
        public ?string $customer = null,
        #[SerializedName('fiscal_data')]
        public ?Receipt $receipt = null,
        #[SerializedName('extra_data')]
        public ?array $extraData = null,
    ) {
    }
}
