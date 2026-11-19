<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Project;
use App\Models\SiteSetting;
use App\Support\PublicContentCache;

final readonly class GetSitemapUrls
{
    public function __construct(private PublicContentCache $cache) {}

    /**
     * @return list<array{loc: string, lastmod: ?string}>
     */
    public function __invoke(): array
    {
        return $this->cache->remember('sitemap', function (): array {
            $projects = Project::query()->published()->featuredFirst()->get(['slug', 'updated_at']);

            return [
                ['loc' => route('home'), 'lastmod' => SiteSetting::current()->updated_at?->toAtomString()],
                ...$projects->map(fn (Project $project): array => [
                    'loc' => route('projects.show', $project->slug),
                    'lastmod' => $project->updated_at?->toAtomString(),
                ])->all(),
            ];
        });
    }
}
