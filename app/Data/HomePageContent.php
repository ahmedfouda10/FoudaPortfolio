<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Certification;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\SkillGroup;
use Illuminate\Database\Eloquent\Collection;

/**
 * Everything the one-page home renders, loaded in a fixed number of queries.
 */
final readonly class HomePageContent
{
    /**
     * @param  Collection<int, Experience>  $experiences
     * @param  Collection<int, SkillGroup>  $skillGroups
     * @param  Collection<int, Service>  $services
     * @param  Collection<int, Project>  $projects
     * @param  Collection<int, Education>  $education
     * @param  Collection<int, Certification>  $certifications
     */
    public function __construct(
        public SiteSetting $settings,
        public Collection $experiences,
        public Collection $skillGroups,
        public Collection $services,
        public Collection $projects,
        public Collection $education,
        public Collection $certifications,
    ) {}

    /**
     * Ids of the sections that have something to show, in page order.
     * Drives the rendered sections and the navigation links.
     *
     * @return list<string>
     */
    public function sections(): array
    {
        $sections = [
            'about' => filled($this->settings->about_body) || filled($this->settings->about_highlights),
            'experience' => $this->experiences->isNotEmpty(),
            'skills' => $this->skillGroups->isNotEmpty(),
            'services' => $this->services->isNotEmpty(),
            'projects' => $this->projects->isNotEmpty(),
            'education' => $this->education->isNotEmpty() || $this->certifications->isNotEmpty(),
            'contact' => true,
        ];

        return array_keys(array_filter($sections));
    }

    /**
     * Distinct integrations across the published projects, for the filter.
     *
     * @return list<string>
     */
    public function integrations(): array
    {
        $integrations = [];

        foreach ($this->projects as $project) {
            foreach ($project->integrations ?? [] as $integration) {
                if (is_string($integration) && $integration !== '') {
                    $integrations[$integration] = $integration;
                }
            }
        }

        natcasesort($integrations);

        return array_values($integrations);
    }
}
