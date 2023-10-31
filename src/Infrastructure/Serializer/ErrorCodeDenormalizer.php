<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Infrastructure\Serializer;

use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface as Denormalizer;
use Vanta\Integration\KvellPayout\Response\ErrorCode;

final readonly class ErrorCodeDenormalizer implements Denormalizer
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): ErrorCode
    {
        if (!is_int($data)) {
            throw NotNormalizableValueException::createForUnexpectedDataType(
                sprintf(
                    'Ожидали число, получили: %s',
                    get_debug_type($type)
                ),
                $data,
                [Type::BUILTIN_TYPE_INT],
                $context['deserialization_path'] ?? null,
                true
            );
        }

        return ErrorCode::fromCode($data);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        return ErrorCode::class == $type;
    }
}
