<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\DeletesReplacedFiles;
use App\Observers\FlushPublicContentCache;
use Database\Factories\ProjectImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(FlushPublicContentCache::class)]
#[Fillable(['project_id', 'path', 'alt', 'caption', 'sort_order'])]
class ProjectImage extends Model
{
    use DeletesReplacedFiles;

    /** @use HasFactory<ProjectImageFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return array<string, string>
     */
    protected function fileAttributes(): array
    {
        return [
            'path' => 'public',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
