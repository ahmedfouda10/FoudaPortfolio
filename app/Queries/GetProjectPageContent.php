<?php

declare(strict_types=1);

namespace App\Queries;

use App\Data\ProjectPageContent;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Support\PublicContentCache;

final readonly class GetProjectPageContent
{
    public function __construct(private PublicContentCache $cache) {}

    /**
     * Returns null for unknown or unpublished projects.
     */
    public function __invoke(string $slug): ?ProjectPageContent
    {
        return $this->cache->remember("project:{$slug}", function () use ($slug): ?ProjectPageContent {
            $project = Project::query()->published()->where('slug', $slug)->with('images')->first();

            if ($project === null) {
                return null;
            }

            $published = Project::query()->published()->featuredFirst()->get(['id', 'slug', 'title', 'subtitle']);
            $position = (int) $published->search(fn (Project $candidate): bool => $candidate->is($project));
            $next = $published->count() > 1 ? $published->get(($position + 1) % $published->count()) : null;

            return new ProjectPageContent($project, $next, SiteSetting::current());
        });
    }
}
