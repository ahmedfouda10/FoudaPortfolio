<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ProjectLinkType;
use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->company();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'subtitle' => fake()->sentence(4),
            'tagline' => fake()->sentence(8),
            'summary' => fake()->paragraph(),
            'company' => fake()->company(),
            'year' => (int) fake()->year(),
            'status' => fake()->randomElement(ProjectStatus::cases()),
            'problem' => fake()->paragraph(),
            'my_role' => fake()->sentence(),
            'solution' => '<p>'.fake()->paragraph().'</p>',
            'results' => '<p>'.fake()->paragraph().'</p>',
            'tech_stack' => ['Laravel', 'MySQL'],
            'integrations' => ['Shopify'],
            'cover_image_alt' => $title,
            'links' => [
                ['label' => 'Website', 'url' => fake()->url(), 'type' => ProjectLinkType::Website->value],
            ],
            'is_featured' => false,
            'is_published' => true,
            'sort_order' => 0,
        ];
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true]);
    }

    public function unpublished(): static
    {
        return $this->state(['is_published' => false]);
    }
}
