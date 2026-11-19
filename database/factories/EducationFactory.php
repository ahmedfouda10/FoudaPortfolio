<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Education;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Education>
 */
class EducationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->numberBetween(2000, 2020);

        return [
            'institution' => fake()->company().' University',
            'degree' => 'B.Sc. in '.fake()->randomElement(['Computer Science', 'Information Systems', 'Software Engineering']),
            'field' => fake()->randomElement(['Information Systems', 'Computer Science']),
            'start_year' => $start,
            'end_year' => $start + 4,
            'description' => fake()->sentence(),
            'sort_order' => 0,
            'is_visible' => true,
        ];
    }

    public function hidden(): static
    {
        return $this->state(['is_visible' => false]);
    }
}
