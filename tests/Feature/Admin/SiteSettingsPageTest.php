<?php

declare(strict_types=1);

use App\Filament\Pages\ManageSiteSettings;
use App\Models\SiteSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(fn () => actingAsAdmin());

it('shows the current settings', function (): void {
    SiteSetting::factory()->create(['full_name' => 'Ahmed Fouda', 'years_of_experience' => 4]);

    $this->get('/admin/site-settings')->assertOk();

    Livewire::test(ManageSiteSettings::class)
        ->assertSchemaStateSet(['full_name' => 'Ahmed Fouda', 'years_of_experience' => 4]);
});

it('saves the settings', function (): void {
    SiteSetting::factory()->create();

    Livewire::test(ManageSiteSettings::class)
        ->fillForm([
            'headline' => 'Senior Backend Developer',
            'projects_shipped' => 12,
            'about_highlights' => [['highlight' => 'Shopify apps'], ['highlight' => 'ERP integrations']],
            'github_url' => 'https://github.com/example',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    expect(SiteSetting::current())
        ->headline->toBe('Senior Backend Developer')
        ->projects_shipped->toBe(12)
        ->about_highlights->toBe(['Shopify apps', 'ERP integrations'])
        ->github_url->toBe('https://github.com/example');
});

it('validates the settings', function (): void {
    SiteSetting::factory()->create();

    Livewire::test(ManageSiteSettings::class)
        ->fillForm(['full_name' => '', 'linkedin_url' => 'not-a-url', 'email' => 'nope', 'years_of_experience' => -1])
        ->call('save')
        ->assertHasFormErrors([
            'full_name' => 'required',
            'linkedin_url' => 'url',
            'email' => 'email',
            'years_of_experience' => 'min',
        ]);
});

it('stores the CV privately and the avatar publicly', function (): void {
    Storage::fake('local');
    Storage::fake('public');
    SiteSetting::factory()->create();

    Livewire::test(ManageSiteSettings::class)
        ->fillForm([
            'cv_file' => UploadedFile::fake()->create('ahmed-fouda-cv.pdf', 200, 'application/pdf'),
            'avatar' => UploadedFile::fake()->image('avatar.jpg', 400, 400),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = SiteSetting::current();

    expect($settings->cv_file)->toStartWith('cv/')
        ->and($settings->avatar)->toStartWith('profile/');

    Storage::disk('local')->assertExists((string) $settings->cv_file);
    Storage::disk('public')->assertExists((string) $settings->avatar);
});

it('only accepts a PDF as the CV', function (): void {
    Storage::fake('local');
    SiteSetting::factory()->create();

    Livewire::test(ManageSiteSettings::class)
        ->fillForm(['cv_file' => UploadedFile::fake()->create('cv.exe', 10, 'application/octet-stream')])
        ->call('save')
        ->assertHasFormErrors(['cv_file']);
});
