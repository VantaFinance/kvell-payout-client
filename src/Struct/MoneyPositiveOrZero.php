<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Struct;

use Money\Money;

final readonly class MoneyPositiveOrZero implements \Stringable
{
    public function __construct(
        public Money $value
    ) {
        if (!$value->isPositive() && !$value->isZero()) {
            throw new \InvalidArgumentException('Ожидали позитивное число или ноль');
        }
    }

    /**
     * @return numeric-string
     */
    public function getAmount(): string
    {
        return $this->value->getAmount();
    }

    /**
     * @return numeric-string
     */
    public function __toString(): string
    {
        return $this->value->getAmount();
    }
}
