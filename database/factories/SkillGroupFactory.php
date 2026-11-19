<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SkillGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SkillGroup>
 */
class SkillGroupFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->jobTitle(),
            'sort_order' => 0,
            'is_visible' => true,
        ];
    }

    public function hidden(): static
    {
        return $this->state(['is_visible' => false]);
    }
}
