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

final readonly class Client
{
    public function __construct(
        public ?PhoneNumber $phoneNumber = null,
        public ?Email $email = null
    ) {
        if (null == $phoneNumber && null == $email) {
            throw new \InvalidArgumentException('Номер телефона или email должен быть заполнен');
        }
    }
}
