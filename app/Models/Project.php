<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProjectStatus;
use App\Models\Concerns\DeletesReplacedFiles;
use App\Models\Concerns\HasSortOrder;
use App\Observers\FlushPublicContentCache;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A portfolio case study.
 *
 * `links` is a list of `{label, url, type}` items where `type` is a ProjectLinkType value.
 */
#[ObservedBy(FlushPublicContentCache::class)]
#[Fillable([
    'title', 'slug', 'subtitle', 'tagline', 'summary', 'company', 'year', 'status',
    'problem', 'my_role', 'solution', 'results', 'tech_stack', 'integrations',
    'cover_image', 'cover_image_alt', 'links',
    'meta_title', 'meta_description',
    'is_featured', 'is_published', 'sort_order',
])]
class Project extends Model
{
    use DeletesReplacedFiles;

    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    use HasSortOrder;

    /**
     * Gallery rows are removed through their models (not only by the FK cascade)
     * so their files are deleted from disk too.
     */
    protected static function booted(): void
    {
        static::deleting(function (Project $project): void {
            $project->images()->get()->each(fn (ProjectImage $image): ?bool => $image->delete());
        });
    }

    /**
     * Gallery images, in the order arranged in the dashboard.
     *
     * @return HasMany<ProjectImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @param  Builder<static>  $query
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where($this->qualifyColumn('is_published'), true);
    }

    /**
     * @param  Builder<static>  $query
     */
    #[Scope]
    protected function featured(Builder $query): void
    {
        $query->where($this->qualifyColumn('is_featured'), true);
    }

    /**
     * Featured projects first, then the manual order from the dashboard.
     *
     * @param  Builder<static>  $query
     */
    #[Scope]
    protected function featuredFirst(Builder $query): void
    {
        $query->orderByDesc($this->qualifyColumn('is_featured'))->ordered();
    }

    /**
     * @return array<string, string>
     */
    protected function fileAttributes(): array
    {
        return [
            'cover_image' => 'public',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'year' => 'integer',
            'tech_stack' => 'array',
            'integrations' => 'array',
            'links' => 'array',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
            'views_count' => 'integer',
        ];
    }
}
