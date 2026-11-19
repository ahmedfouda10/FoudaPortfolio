<?php

declare(strict_types=1);

namespace App\Support;

use SensitiveParameter;

/**
 * Turns an IP address into a keyed, irreversible fingerprint so visitors can
 * be counted and rate-limited without ever storing their real address.
 */
final readonly class IpHasher
{
    public function __construct(
        #[SensitiveParameter] private string $key,
    ) {}

    public function hash(?string $ip): ?string
    {
        if ($ip === null || $ip === '') {
            return null;
        }

        return hash_hmac('sha256', $ip, $this->key);
    }
}
