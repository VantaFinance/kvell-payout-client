<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Infrastructure\Serializer;

use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface as Normalizer;
use Vanta\Integration\KvellPayout\Request\Card;

final readonly class CardNormalizer  implements Normalizer
{
    public function normalize(mixed $object, string $format = null, array $context = []): string
    {
        if (!$object instanceof Card) {
            throw new UnexpectedValueException(sprintf('Allowed type: %s', Card::class));
        }

        return $object->value;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof Card;
    }
}
