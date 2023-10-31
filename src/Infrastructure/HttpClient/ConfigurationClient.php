<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Infrastructure\HttpClient;

use Vanta\Integration\KvellPayout\Struct\SignKey;

final readonly class ConfigurationClient
{
    /**
     * @param non-empty-string $apiKey
     * @param non-empty-string $url
     */
    public function __construct(
        public SignKey $signKey,
        public string $apiKey,
        public string $secretKey,
        public string $url
    ) {
    }
}
