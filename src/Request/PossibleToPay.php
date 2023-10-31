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

final readonly class PossibleToPay
{
    public function __construct(
        #[SerializedName('fio')]
        public string $fullName,
        #[SerializedName('bank_id')]
        public string $bankId,
        #[SerializedName('phone')]
        public PhoneNumber $phoneNumber,
        public MoneyPositive $amount,
        #[SerializedName('fio_check')]
        public bool $fullNameCheck = false,
    ) {
    }
}
