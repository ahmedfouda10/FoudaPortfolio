<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

/**
 * For models whose order is arranged by drag and drop in the dashboard.
 */
trait HasSortOrder
{
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
