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

final readonly class Bank
{
    /**
     * @param numeric-string   $id
     * @param non-empty-string $name
     */
    public function __construct(
        #[SerializedName('bank_id')]
        public string $id,
        public string $name,
    ) {
    }
}
