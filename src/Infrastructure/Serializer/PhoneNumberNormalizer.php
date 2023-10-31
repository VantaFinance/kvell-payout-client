<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Infrastructure\Serializer;

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberType;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface as Denormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface as Normalizer;

final readonly class PhoneNumberNormalizer implements Normalizer, Denormalizer
{
    public const PHONE_NUMBER_TYPE = 'pos_credit.phone_number_type';

    /**
     * @param array<string, mixed> $context
     */
    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return PhoneNumber::class == $type;
    }

    /**
     * @param array{
     *     "pos_credit.phone_number_type"?: positive-int,
     *     deserialization_path?: non-empty-string
     * } $context
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): PhoneNumber
    {
        if (!\is_string($data)) {
            throw NotNormalizableValueException::createForUnexpectedDataType(
                sprintf('Ожидали строку,получили:%s.', get_debug_type($data)),
                $data,
                [Type::BUILTIN_TYPE_STRING],
                $context['deserialization_path'] ?? null,
                true
            );
        }

        try {
            $phoneNumber = PhoneNumber::parse($data);
        } catch (PhoneNumberParseException $e) {
            throw NotNormalizableValueException::createForUnexpectedDataType(
                $e->getMessage(),
                $data,
                [$type],
                $context['deserialization_path'] ?? null,
                true,
                0,
                $e
            );
        }

        $allowedTypePhoneNumber = $context[self::PHONE_NUMBER_TYPE] ?? false;

        if (!$allowedTypePhoneNumber) {
            return $phoneNumber;
        }

        if ($allowedTypePhoneNumber != $phoneNumber->getNumberType()) {
            /** @var array<non-empty-string, int> $types */
            $types = (new \ReflectionClass(PhoneNumberType::class))->getConstants();
            $types = array_flip($types);

            throw NotNormalizableValueException::createForUnexpectedDataType(
                sprintf(
                    'Ожидали номер тип номера телефона: %s, получили: %s',
                    $types[$allowedTypePhoneNumber],
                    $types[$phoneNumber->getNumberType()]
                ),
                $data,
                [Type::BUILTIN_TYPE_STRING],
                $context['deserialization_path'] ?? null,
                true
            );
        }

        return $phoneNumber;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof PhoneNumber;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function normalize(mixed $object, string $format = null, array $context = []): string
    {
        if (!$object instanceof PhoneNumber) {
            throw new UnexpectedValueException(sprintf('Allowed type: %s', PhoneNumber::class));
        }

        return str_replace('+', '', $object->jsonSerialize());
    }
}
