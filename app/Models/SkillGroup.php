<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use App\Models\Concerns\HasVisibility;
use App\Observers\FlushPublicContentCache;
use Database\Factories\SkillGroupFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(FlushPublicContentCache::class)]
#[Fillable(['name', 'icon', 'sort_order', 'is_visible'])]
class SkillGroup extends Model
{
    /** @use HasFactory<SkillGroupFactory> */
    use HasFactory;

    use HasSortOrder;
    use HasVisibility;

    /**
     * @return HasMany<Skill, $this>
     */
    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_visible' => 'boolean',
        ];
    }
}
