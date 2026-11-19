<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Project;
use App\Models\SiteSetting;

final readonly class ProjectPageContent
{
    public function __construct(
        public Project $project,
        public ?Project $nextProject,
        public SiteSetting $settings,
    ) {}
}
