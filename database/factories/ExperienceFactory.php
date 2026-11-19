<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EmploymentType;
use App\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Experience>
 */
class ExperienceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-8 years', '-1 year');

        return [
            'company' => fake()->company(),
            'role' => fake()->jobTitle(),
            'location' => fake()->city(),
            'employment_type' => fake()->randomElement(EmploymentType::cases()),
            'start_date' => $start,
            'end_date' => fake()->dateTimeBetween($start, 'now'),
            'description' => fake()->sentence(),
            'bullet_points' => fake()->sentences(3),
            'sort_order' => 0,
            'is_visible' => true,
        ];
    }

    public function current(): static
    {
        return $this->state(['end_date' => null]);
    }

    public function hidden(): static
    {
        return $this->state(['is_visible' => false]);
    }
}
