<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CvDownload;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CvDownload>
 */
class CvDownloadFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ip_hash' => hash('sha256', fake()->ipv4()),
            'user_agent' => fake()->userAgent(),
            'referer' => fake()->optional()->url(),
            'created_at' => fake()->dateTimeBetween('-60 days'),
        ];
    }
}
