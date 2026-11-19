<?php

declare(strict_types=1);

namespace App\Queries;

use App\Data\HomePageContent;
use App\Models\Certification;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\SkillGroup;
use App\Support\PublicContentCache;
use Illuminate\Database\Eloquent\Relations\HasMany;

final readonly class GetHomePageContent
{
    public function __construct(private PublicContentCache $cache) {}

    public function __invoke(): HomePageContent
    {
        return $this->cache->remember('home', fn (): HomePageContent => new HomePageContent(
            settings: SiteSetting::current(),
            experiences: Experience::query()->visible()->ordered()->get(),
            skillGroups: SkillGroup::query()
                ->visible()
                ->ordered()
                ->with(['skills' => fn (HasMany $query): HasMany => $query->visible()->ordered()])
                ->get()
                ->filter(fn (SkillGroup $group): bool => $group->skills->isNotEmpty())
                ->values(),
            services: Service::query()->visible()->ordered()->get(),
            projects: Project::query()->published()->featuredFirst()->get(),
            education: Education::query()->visible()->ordered()->get(),
            certifications: Certification::query()->visible()->ordered()->get(),
        ));
    }
}
