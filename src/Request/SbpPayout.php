<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Request;

use Brick\PhoneNumber\PhoneNumber;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Vanta\Integration\KvellPayout\Struct\MoneyPositive;

final class SbpPayout
{
    /**
     * @param numeric-string   $bankId
     * @param non-empty-string $fullName
     * @param non-empty-string $description
     * @param non-empty-string $transactionId
     */
    public function __construct(
        public string $description,
        #[SerializedName('transaction')]
        public string $transactionId,
        public MoneyPositive $amount,
        #[SerializedName('fio')]
        public string $fullName,
        #[SerializedName('bank_id')]
        public string $bankId,
        #[SerializedName('phone')]
        public PhoneNumber $phoneNumber,
    ) {
    }
}
