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
use Vanta\Integration\KvellPayout\Struct\MoneyPositiveOrZero;

final readonly class OrderItem
{
    /**
     * @param non-empty-string      $name
     * @param non-empty-string|null $measurementUnit
     */
    public function __construct(
        public string $name,
        public MoneyPositiveOrZero $price,
        public float|int $quantity,
        #[SerializedName('payment_method')]
        public PaymentMethod $paymentMethod,
        #[SerializedName('payment_object')]
        public PaymentObject $paymentObject,
        #[SerializedName('vat_type')]
        public TaxRate $taxRate,
        public ?MoneyPositiveOrZero $sum = null,
        #[SerializedName('measurement_unit')]
        public ?string $measurementUnit = null,
    ) {
    }
}
