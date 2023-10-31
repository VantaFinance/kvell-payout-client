<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Request;

final readonly class Card
{
    /**
     * @param non-empty-string $value
     */
    public function __construct(
        public string $value,
    ) {
        if (!ctype_digit($value)) {
            throw new \InvalidArgumentException('Не валидный номер карты');
        }

        $checkSum = 0;
        $length   = \strlen($value);

        for ($i = $length - 1; $i >= 0; --$i) {
            if (($i % 2) ^ ($length % 2)) {
                // Starting with the last digit and walking left, add every second
                // digit to the check sum
                // e.g. 7  9  9  2  7  3  9  8  7  1  3
                //      ^     ^     ^     ^     ^     ^
                //    = 7  +  9  +  7  +  9  +  7  +  3
                $checkSum += (int) $value[$i];
            } else {
                // Starting with the second last digit and walking left, double every
                // second digit and add it to the check sum
                // For doubles greater than 9, sum the individual digits
                // e.g. 7  9  9  2  7  3  9  8  7  1  3
                //         ^     ^     ^     ^     ^
                //    =    1+8 + 4  +  6  +  1+6 + 2
                /**@phpstan-ignore-next-line */
                $checkSum += (((int) (2 * $value[$i] / 10)) + (2 * $value[$i]) % 10);
            }
        }

        if (0 === $checkSum || 0 !== $checkSum % 10) {
            throw new \InvalidArgumentException('Не валидный номер карты');
        }
    }
}
