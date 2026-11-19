<?php

declare(strict_types=1);

namespace App\Support\Seo;

use App\Data\HomePageContent;
use App\Data\ProjectPageContent;
use App\Models\SiteSetting;
use App\Support\Images\ImageVariants;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Everything a page needs for search engines and social previews:
 * title, description, canonical URL, Open Graph / Twitter image and JSON-LD.
 */
final readonly class SeoData
{
    /**
     * @param  list<array<string, mixed>>  $structuredData
     */
    public function __construct(
        public string $title,
        public ?string $description,
        public string $url,
        public string $siteName,
        public string $type = 'website',
        public ?string $image = null,
        public ?string $imageAlt = null,
        public array $structuredData = [],
    ) {}

    public static function forHome(HomePageContent $content): self
    {
        $settings = $content->settings;

        return new self(
            title: $settings->meta_title ?: self::defaultTitle($settings),
            description: self::clean($settings->meta_description ?: $settings->short_bio),
            url: route('home'),
            siteName: $settings->full_name,
            image: self::shareImage($settings),
            imageAlt: $settings->full_name,
            structuredData: [
                StructuredData::person($content),
                StructuredData::website($settings),
            ],
        );
    }

    public static function forProject(ProjectPageContent $page): self
    {
        $project = $page->project;
        $settings = $page->settings;
        $cover = self::rasterImageUrl($project->cover_image);

        return new self(
            title: $project->meta_title ?: "{$project->title} — {$settings->full_name}",
            description: self::clean($project->meta_description ?: ($project->summary ?? $project->tagline)),
            url: route('projects.show', $project->slug),
            siteName: $settings->full_name,
            type: 'article',
            image: $cover ?? self::shareImage($settings),
            imageAlt: $cover !== null ? ($project->cover_image_alt ?? $project->title) : $settings->full_name,
            structuredData: [
                StructuredData::creativeWork($project, $settings),
                StructuredData::breadcrumbs($project),
            ],
        );
    }

    public static function defaultTitle(SiteSetting $settings): string
    {
        return trim("{$settings->full_name} — {$settings->headline}", ' —');
    }

    /**
     * The share image from the settings, falling back to the profile photo.
     */
    private static function shareImage(SiteSetting $settings): ?string
    {
        return self::rasterImageUrl($settings->og_image) ?? self::rasterImageUrl($settings->avatar);
    }

    /**
     * Social networks do not render SVG previews, so only raster images qualify.
     */
    private static function rasterImageUrl(?string $path): ?string
    {
        if ($path === null || $path === '' || ! app(ImageVariants::class)->supports($path)) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }

    private static function clean(?string $text): ?string
    {
        if ($text === null || trim($text) === '') {
            return null;
        }

        return Str::limit(trim((string) preg_replace('/\s+/', ' ', strip_tags($text))), 160);
    }
}
