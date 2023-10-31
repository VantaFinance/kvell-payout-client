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

final readonly class Company
{
    /**
     * @param non-empty-string|null $paymentAddress
     */
    public function __construct(
        public Email $email,
        #[SerializedName('sno')]
        public TaxSystem $taxSystem,
        #[SerializedName('payment_address')]
        public ?string $paymentAddress = null,
    ) {
    }
}
