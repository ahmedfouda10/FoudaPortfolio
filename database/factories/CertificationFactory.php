<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Certification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Certification>
 */
class CertificationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle().' Certificate',
            'issuer' => fake()->company(),
            'started_on' => null,
            'completed_on' => fake()->dateTimeBetween('-5 years'),
            'is_in_progress' => false,
            'credential_urls' => [fake()->url()],
            'description' => fake()->optional()->sentence(),
            'sort_order' => 0,
            'is_visible' => true,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(['completed_on' => null, 'is_in_progress' => true]);
    }

    public function hidden(): static
    {
        return $this->state(['is_visible' => false]);
    }
}
