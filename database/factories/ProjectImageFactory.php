<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectImage>
 */
class ProjectImageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'path' => 'projects/gallery/'.fake()->uuid().'.webp',
            'alt' => fake()->sentence(4),
            'caption' => fake()->optional()->sentence(),
            'sort_order' => 0,
        ];
    }
}
