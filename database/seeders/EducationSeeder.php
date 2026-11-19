<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Education;
use Illuminate\Database\Seeder;

final class EducationSeeder extends Seeder
{
    public function run(): void
    {
        Education::query()->firstOrCreate(
            ['institution' => 'Kafr El-Sheikh University', 'degree' => 'B.Sc. in Computers and Information'],
            [
                'field' => 'Information Systems',
                'start_year' => 2016,
                'end_year' => 2020,
                'description' => 'Graduation project: web application for diagnosing fish diseases (Laravel, MySQL) — Team Leader & Backend Developer.',
                'sort_order' => 1,
                'is_visible' => true,
            ],
        );
    }
}
