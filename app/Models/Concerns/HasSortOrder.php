<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * For models whose order is arranged by drag and drop in the dashboard.
 */
trait HasSortOrder
{
    /**
     * New records without an explicit position are appended to the end of the list.
     */
    protected static function bootHasSortOrder(): void
    {
        static::creating(function (Model $model): void {
            if (array_key_exists('sort_order', $model->getAttributes())) {
                return;
            }

            $model->setAttribute('sort_order', (int) $model->newQuery()->max('sort_order') + 1);
        });
    }

    /**
     * @param  Builder<static>  $query
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query
            ->orderBy($this->qualifyColumn('sort_order'))
            ->orderBy($this->getQualifiedKeyName());
    }
}
