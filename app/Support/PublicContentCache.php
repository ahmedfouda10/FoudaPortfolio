<?php

declare(strict_types=1);

namespace App\Support;

use Closure;
use Illuminate\Contracts\Cache\Repository;

/**
 * Caches the data behind the public pages.
 *
 * Every key is prefixed with a version number. Flushing simply bumps the
 * version, which invalidates all public entries at once on any cache store
 * (no tags needed); stale entries expire on their own.
 */
final readonly class PublicContentCache
{
    private const string VERSION_KEY = 'public-content:version';

    private const int TTL_SECONDS = 60 * 60 * 24 * 7;

    public function __construct(private Repository $cache) {}

    /**
     * @template TValue
     *
     * @param  Closure(): TValue  $callback
     * @return TValue
     */
    public function remember(string $key, Closure $callback): mixed
    {
        return $this->cache->remember($this->key($key), self::TTL_SECONDS, $callback);
    }

    public function flush(): void
    {
        $this->cache->forever(self::VERSION_KEY, $this->version() + 1);
    }

    public function key(string $key): string
    {
        return "public-content:v{$this->version()}:{$key}";
    }

    private function version(): int
    {
        $version = $this->cache->get(self::VERSION_KEY, 1);

        return is_int($version) ? $version : 1;
    }
}
