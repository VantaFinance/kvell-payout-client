<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Infrastructure\Serializer;

use Money\Currency;
use Money\Money;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface as Denormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface as Normalizer;
use Vanta\Integration\KvellPayout\Struct\MoneyPositiveOrZero;

final readonly class MoneyPositiveOrZeroNormalizer implements Normalizer, Denormalizer
{
    /**
     * @psalm-suppress MissingParamType
     *
     * @param array<string, mixed> $context
     */
    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return MoneyPositiveOrZero::class == $type;
    }

    /**
     * @psalm-suppress MissingParamType
     *
     * @param array{deserialization_path?: non-empty-string} $context
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): MoneyPositiveOrZero
    {
        if ('' == $data) {
            throw NotNormalizableValueException::createForUnexpectedDataType(
                'Ожидали не пустую строку',
                $data,
                [Type::BUILTIN_TYPE_STRING],
                $context['deserialization_path'] ?? null,
                true
            );
        }

        if (!is_numeric($data)) {
            throw NotNormalizableValueException::createForUnexpectedDataType(
                'Ожидали число в виде строки',
                $data,
                [Type::BUILTIN_TYPE_STRING],
                $context['deserialization_path'] ?? null,
                true
            );
        }

        try {
            return new MoneyPositiveOrZero(new Money((string) $data, new Currency('RUB')));
        } catch (\InvalidArgumentException $e) {
            throw NotNormalizableValueException::createForUnexpectedDataType(
                $e->getMessage(),
                $data,
                [Type::BUILTIN_TYPE_INT, Type::BUILTIN_TYPE_STRING],
                $context['deserialization_path'] ?? null,
                true
            );
        }
    }

    /**
     * @psalm-suppress MissingParamType
     *
     * @param array<string, mixed> $context
     */
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof MoneyPositiveOrZero;
    }

    public function normalize($object, string $format = null, array $context = []): int
    {
        if (!$object instanceof MoneyPositiveOrZero) {
            throw new UnexpectedValueException(sprintf('Allowed type: %s', MoneyPositiveOrZero::class));
        }

        return (int) $object->getAmount();
    }
}
