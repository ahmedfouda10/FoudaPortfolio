<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SiteSetting>
 */
class SiteSettingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'headline' => fake()->jobTitle(),
            'short_bio' => fake()->sentence(16),
            'location' => fake()->city().', '.fake()->country(),
            'availability_status' => 'Open to new opportunities',
            'email' => fake()->safeEmail(),
            'linkedin_url' => 'https://www.linkedin.com/in/'.fake()->userName(),
            'github_url' => 'https://github.com/'.fake()->userName(),
            'years_of_experience' => fake()->numberBetween(1, 15),
            'projects_shipped' => fake()->numberBetween(1, 50),
            'platforms_integrated' => fake()->numberBetween(1, 10),
            'about_body' => '<p>'.fake()->paragraph().'</p>',
            'about_highlights' => fake()->sentences(3),
            'meta_title' => fake()->sentence(4),
            'meta_description' => fake()->sentence(12),
        ];
    }
}
