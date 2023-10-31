<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Infrastructure\Serializer;

use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Vanta\Integration\KvellPayout\Response\PayoutClassic;
use Vanta\Integration\KvellPayout\Response\PayoutOtp;

final class PayoutDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    private const DENORMALIZED = 'pos_credit.denormalized';

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): ?object
    {
        if (!is_array($data)) {
            return null;
        }

        if (!array_key_exists('order', $data) && !array_key_exists('status', $data)) {
            return null;
        }

        $newContext = array_merge([self::DENORMALIZED => true], $context);

        if (($data['order'] ?? null) !== null && PayoutClassic::class == $type) {
            return $this->denormalizer->denormalize($data, PayoutClassic::class, context: $newContext);
        }

        if (($data['status'] ?? null) !== null && PayoutOtp::class == $type) {
            return $this->denormalizer->denormalize($data, PayoutClassic::class, context: $newContext);
        }

        return null;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return in_array($type, [PayoutClassic::class, PayoutOtp::class]) && !array_key_exists(self::DENORMALIZED, $context);
    }
}
