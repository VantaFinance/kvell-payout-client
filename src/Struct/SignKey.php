<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Struct;

final readonly class SignKey
{
    /**
     * @param non-empty-string      $value
     * @param non-empty-string|null $passphrase
     */
    public function __construct(
        public string $value,
        public ?string $passphrase = null
    ) {
    }

    public function toOpensslPrivateKey(): \OpenSSLAsymmetricKey
    {
        $key = openssl_pkey_get_private($this->value, $this->passphrase);

        return false === $key ? throw new \RuntimeException('Invalid sign key') : $key;
    }
}
