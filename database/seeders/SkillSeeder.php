<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SkillGroup;
use Illuminate\Database\Seeder;

final class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            'Backend' => ['PHP 8+', 'Laravel 10/11/12', 'OOP', 'MVC', 'RESTful APIs', 'GraphQL', 'Magento 2'],
            'Databases' => ['MySQL', 'Database design', 'Query optimization', 'Caching'],
            'Integrations' => ['Shopify', 'Salla', 'Zid', 'ERPNext', 'Magento', 'OpenAI API', 'Webhooks', 'Email & SMS gateways'],
            'Testing & DevOps' => ['PHPUnit', 'Pest', 'Git', 'GitHub', 'GitLab CI/CD', 'Docker'],
            'Frontend' => ['HTML5', 'CSS3', 'JavaScript', 'jQuery', 'Ajax', 'Bootstrap', 'Livewire', 'Tailwind CSS'],
            'Admin panels' => ['Filament'],
        ];

        $groupOrder = 0;

        foreach ($groups as $groupName => $skills) {
            $group = SkillGroup::query()->firstOrCreate(
                ['name' => $groupName],
                ['sort_order' => ++$groupOrder, 'is_visible' => true],
            );

            foreach ($skills as $skillOrder => $skillName) {
                $group->skills()->firstOrCreate(
                    ['name' => $skillName],
                    ['sort_order' => $skillOrder + 1, 'is_visible' => true],
                );
            }
        }
    }
}
