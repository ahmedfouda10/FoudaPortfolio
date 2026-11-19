<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Database\Seeder;

final class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            [
                'company' => 'Croco IT',
                'role' => 'Backend Developer (PHP Laravel)',
                'start_date' => '2023-02-01',
                'end_date' => null,
                'bullet_points' => [
                    'Design, build and maintain backend systems and RESTful APIs for multiple client products using PHP and Laravel.',
                    "Built and published e-commerce apps on the Shopify App Store and Salla App Store, passing each platform's app review and approval process.",
                    'Integrated Laravel applications with Shopify, Salla, Magento and ERPNext (authentication, webhooks, data sync for products, orders and customers); currently adding Zid.',
                    'Worked on Magento projects during a dedicated phase, handling customization and integration with external systems.',
                    'Built AI-powered features using the OpenAI API, such as automated product content and image generation.',
                    'Optimized MySQL queries and added caching layers to improve application performance.',
                    'Work with GitLab CI/CD pipelines for automated builds, testing and deployments.',
                ],
            ],
            [
                'company' => 'Qeema Tech',
                'role' => 'Backend Developer (PHP Laravel)',
                'start_date' => '2022-06-01',
                'end_date' => '2023-02-01',
                'bullet_points' => [
                    'Developed backend features and modules for web applications using PHP and Laravel.',
                    'Designed and optimized MySQL database schemas and queries.',
                    'Built and integrated RESTful APIs consumed by web and mobile clients.',
                ],
            ],
        ];

        foreach ($positions as $index => $position) {
            Experience::query()->firstOrCreate(
                ['company' => $position['company'], 'role' => $position['role']],
                [
                    ...$position,
                    // TODO: location and employment type were not provided; fill them in from the dashboard.
                    'location' => null,
                    'employment_type' => null,
                    'sort_order' => $index + 1,
                    'is_visible' => true,
                ],
            );
        }
    }
}
