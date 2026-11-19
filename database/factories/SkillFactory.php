<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Skill;
use App\Models\SkillGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'skill_group_id' => SkillGroup::factory(),
            'name' => fake()->unique()->word(),
            'sort_order' => 0,
            'is_visible' => true,
        ];
    }

    public function hidden(): static
    {
        return $this->state(['is_visible' => false]);
    }
}
