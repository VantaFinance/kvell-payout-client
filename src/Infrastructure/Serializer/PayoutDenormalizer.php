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
use Vanta\Integration\KvellPayout\Response\Order;
use Vanta\Integration\KvellPayout\Response\TransactionStatus;

final class PayoutDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    public const TRANSFORM = 'vanta.payout_transform';

    private const DENORMALIZED = 'vanta.denormalized';

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): ?object
    {
        if (!is_array($data)) {
            return null;
        }

        if (!array_key_exists('order', $data) && !array_key_exists('status', $data)) {
            return null;
        }

        $newContext = array_merge([self::DENORMALIZED => true], $context);

        if (($data['order'] ?? null) !== null && Order::class == $type) {
            return $this->denormalizer->denormalize($data['order'], Order::class, context: $newContext);
        }

        if (($data['status'] ?? null) !== null && TransactionStatus::class == $type) {
            return $this->denormalizer->denormalize($data['status'], TransactionStatus::class, context: $newContext);
        }

        return null;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return array_key_exists(self::TRANSFORM, $context) && !array_key_exists(self::DENORMALIZED, $context);
    }
}
