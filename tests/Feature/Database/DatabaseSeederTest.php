<?php

declare(strict_types=1);

use App\Models\Certification;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\SkillGroup;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('public');

    config([
        'portfolio.admin.name' => 'Site Owner',
        'portfolio.admin.email' => 'owner@example.com',
        'portfolio.admin.password' => 'a-strong-password',
        'portfolio.contact.email' => null,
        'portfolio.contact.phone' => null,
        'portfolio.contact.whatsapp' => null,
    ]);
});

/**
 * @return array<string, int>
 */
function contentCounts(): array
{
    return [
        'users' => User::query()->count(),
        'settings' => SiteSetting::query()->count(),
        'experiences' => Experience::query()->count(),
        'skill groups' => SkillGroup::query()->count(),
        'skills' => Skill::query()->count(),
        'services' => Service::query()->count(),
        'projects' => Project::query()->count(),
        'education' => Education::query()->count(),
        'certifications' => Certification::query()->count(),
    ];
}

it('seeds the real portfolio content', function (): void {
    $this->seed(DatabaseSeeder::class);

    expect(contentCounts())->toBe([
        'users' => 1,
        'settings' => 1,
        'experiences' => 2,
        'skill groups' => 6,
        'skills' => 34,
        'services' => 6,
        'projects' => 4,
        'education' => 1,
        'certifications' => 3,
    ]);

    expect(SiteSetting::current())
        ->full_name->toBe('Ahmed Fouda')
        ->years_of_experience->toBe(4);

    expect(Project::query()->published()->featured()->ordered()->pluck('slug')->all())
        ->toBe(['2loyal', 'conte']);
});

it('gives every seeded project a cover: a website screenshot or a placeholder', function (): void {
    $this->seed(DatabaseSeeder::class);

    Project::query()->each(function (Project $project): void {
        expect($project->cover_image)->not->toBeNull()
            ->and($project->cover_image_alt)->not->toBeEmpty();
        Storage::disk('public')->assertExists((string) $project->cover_image);
    });

    expect(Project::query()->where('slug', '2loyal')->value('cover_image'))->toBe('projects/covers/2loyal.webp')
        ->and(Project::query()->where('slug', 'tracking')->value('cover_image'))->toBe('projects/covers/tracking-placeholder.svg');
});

it('can run again without duplicating or overwriting content', function (): void {
    $this->seed(DatabaseSeeder::class);
    $countsAfterFirstRun = contentCounts();

    Project::query()->where('slug', '2loyal')->update(['title' => 'Edited from the dashboard']);

    $this->seed(DatabaseSeeder::class);

    expect(contentCounts())->toBe($countsAfterFirstRun)
        ->and(Project::query()->where('slug', '2loyal')->value('title'))->toBe('Edited from the dashboard');
});

it('creates the admin user from the environment', function (): void {
    $this->seed(AdminUserSeeder::class);

    $admin = User::query()->sole();

    expect($admin->name)->toBe('Site Owner')
        ->and($admin->email)->toBe('owner@example.com')
        ->and(Hash::check('a-strong-password', $admin->password))->toBeTrue();
});

it('skips the admin user when no password is configured', function (): void {
    config(['portfolio.admin.password' => null]);

    $this->seed(AdminUserSeeder::class);

    expect(User::query()->count())->toBe(0);
});

it('never seeds private contact details unless they are configured', function (): void {
    $this->seed(DatabaseSeeder::class);

    expect(SiteSetting::current())
        ->email->toBeNull()
        ->phone->toBeNull()
        ->whatsapp->toBeNull();
});
