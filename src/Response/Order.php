<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Response;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Vanta\Integration\KvellPayout\Struct\MoneyPositive;
use Vanta\Integration\KvellPayout\Struct\MoneyPositiveOrZero;

final readonly class Order
{
    public function __construct(
        public string $id,
        #[SerializedName('transaction')]
        public string $transactionId,
        public string $description,
        public TransactionStatus $status,
        #[SerializedName('created_at')]
        public \DateTimeImmutable $createdAt,
        public MoneyPositiveOrZero $commission,
        public MoneyPositive $amount,
    ) {
    }
}
