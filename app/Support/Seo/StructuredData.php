<?php

declare(strict_types=1);

namespace App\Support\Seo;

use App\Data\HomePageContent;
use App\Models\Experience;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\SkillGroup;
use Illuminate\Support\Facades\Storage;

/**
 * schema.org JSON-LD for the public pages.
 */
final class StructuredData
{
    /**
     * @return array<string, mixed>
     */
    public static function person(HomePageContent $content): array
    {
        $settings = $content->settings;
        $currentRole = $content->experiences->first(fn (Experience $experience): bool => $experience->isCurrent());

        return self::withoutEmpty([
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            '@id' => self::personId(),
            'name' => $settings->full_name,
            'url' => route('home'),
            'jobTitle' => $settings->headline,
            'description' => $settings->short_bio,
            'image' => filled($settings->avatar) ? Storage::disk('public')->url($settings->avatar) : null,
            'homeLocation' => filled($settings->location) ? ['@type' => 'Place', 'name' => $settings->location] : null,
            'worksFor' => $currentRole !== null ? ['@type' => 'Organization', 'name' => $currentRole->company] : null,
            'knowsAbout' => $content->skillGroups
                ->flatMap(fn (SkillGroup $group) => $group->skills->map(fn (Skill $skill): string => $skill->name))
                ->unique()
                ->values()
                ->all(),
            'sameAs' => array_values(array_filter([$settings->linkedin_url, $settings->github_url, $settings->freelancer_url])),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function website(SiteSetting $settings): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $settings->full_name,
            'url' => route('home'),
            'author' => ['@id' => self::personId()],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function creativeWork(Project $project, SiteSetting $settings): array
    {
        return self::withoutEmpty([
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'name' => $project->title,
            'headline' => $project->tagline ?? $project->title,
            'description' => $project->meta_description ?: $project->summary,
            'url' => route('projects.show', $project->slug),
            'image' => filled($project->cover_image) ? Storage::disk('public')->url($project->cover_image) : null,
            'dateCreated' => $project->year !== null ? (string) $project->year : null,
            'keywords' => implode(', ', [...($project->integrations ?? []), ...($project->tech_stack ?? [])]),
            'creator' => ['@type' => 'Person', '@id' => self::personId(), 'name' => $settings->full_name, 'url' => route('home')],
            'producer' => filled($project->company) ? ['@type' => 'Organization', 'name' => $project->company] : null,
            'sameAs' => array_values(array_map(fn (array $link): string => (string) $link['url'], $project->links ?? [])),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function breadcrumbs(Project $project): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => __('site.nav.home'), 'item' => route('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => __('site.nav.projects'), 'item' => route('home').'#projects'],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $project->title, 'item' => route('projects.show', $project->slug)],
            ],
        ];
    }

    /**
     * Safe to embed in a <script> tag: "<", ">" and "&" are escaped.
     *
     * @param  array<string, mixed>  $data
     */
    public static function toJson(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_THROW_ON_ERROR);
    }

    private static function personId(): string
    {
        return route('home').'#person';
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private static function withoutEmpty(array $data): array
    {
        return array_filter($data, fn (mixed $value): bool => $value !== null && $value !== '' && $value !== []);
    }
}
