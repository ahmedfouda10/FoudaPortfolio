<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Support\PublicContentCache;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seeds the admin user and the real portfolio content.
 *
 * Every seeder is idempotent: re-running it never duplicates rows and never
 * overwrites content that was edited from the dashboard.
 */
final class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            SiteSettingSeeder::class,
            ExperienceSeeder::class,
            SkillSeeder::class,
            ServiceSeeder::class,
            ProjectSeeder::class,
            EducationSeeder::class,
            CertificationSeeder::class,
        ]);

        // Model events are muted while seeding, so refresh the public pages explicitly.
        app(PublicContentCache::class)->flush();
    }
}
