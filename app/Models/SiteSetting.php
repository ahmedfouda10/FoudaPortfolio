<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\DeletesReplacedFiles;
use App\Observers\FlushPublicContentCache;
use Database\Factories\SiteSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Single-row settings: identity, contact links, hero stats, About section and SEO defaults.
 */
#[ObservedBy(FlushPublicContentCache::class)]
#[Fillable([
    'full_name', 'headline', 'short_bio', 'location', 'availability_status', 'avatar', 'cv_file',
    'email', 'phone', 'whatsapp', 'linkedin_url', 'github_url', 'freelancer_url', 'source_code_url',
    'years_of_experience', 'projects_shipped', 'platforms_integrated',
    'about_body', 'about_highlights',
    'meta_title', 'meta_description', 'og_image',
])]
class SiteSetting extends Model
{
    use DeletesReplacedFiles;

    /** @use HasFactory<SiteSettingFactory> */
    use HasFactory;

    /**
     * The one settings row, created with safe defaults if it does not exist yet.
     */
    public static function current(): self
    {
        return self::query()->oldest('id')->firstOrCreate([], [
            'full_name' => (string) config('app.name'),
        ]);
    }

    /**
     * @return array<string, string>
     */
    protected function fileAttributes(): array
    {
        return [
            'avatar' => 'public',
            'og_image' => 'public',
            'cv_file' => 'local',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'years_of_experience' => 'integer',
            'projects_shipped' => 'integer',
            'platforms_integrated' => 'integer',
            'about_highlights' => 'array',
        ];
    }
}
