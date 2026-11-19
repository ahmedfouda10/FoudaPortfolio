<?php

declare(strict_types=1);

namespace App\Observers;

use App\Support\PublicContentCache;
use Illuminate\Database\Eloquent\Model;

/**
 * Invalidates the cached public pages whenever their content changes.
 */
final readonly class FlushPublicContentCache
{
    /**
     * Columns that never affect what the public pages render.
     */
    private const array IGNORED_COLUMNS = ['views_count', 'updated_at'];

    public function __construct(private PublicContentCache $cache) {}

    public function created(Model $model): void
    {
        $this->cache->flush();
    }

    public function updated(Model $model): void
    {
        if (array_diff(array_keys($model->getChanges()), self::IGNORED_COLUMNS) === []) {
            return;
        }

        $this->cache->flush();
    }

    public function deleted(Model $model): void
    {
        $this->cache->flush();
    }
}
