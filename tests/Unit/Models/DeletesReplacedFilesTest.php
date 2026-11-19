<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('public');
    Storage::fake('local');
});

it('deletes the old cover when it is replaced or cleared', function (): void {
    Storage::disk('public')->put('projects/covers/old.webp', 'old');
    Storage::disk('public')->put('projects/covers/new.webp', 'new');
    $project = Project::factory()->create(['cover_image' => 'projects/covers/old.webp']);

    $project->update(['cover_image' => 'projects/covers/new.webp']);
    Storage::disk('public')->assertMissing('projects/covers/old.webp');

    $project->update(['cover_image' => null]);
    Storage::disk('public')->assertMissing('projects/covers/new.webp');
});

it('keeps the file when other attributes change', function (): void {
    Storage::disk('public')->put('projects/covers/cover.webp', 'cover');
    $project = Project::factory()->create(['cover_image' => 'projects/covers/cover.webp']);

    $project->update(['title' => 'Renamed']);

    Storage::disk('public')->assertExists('projects/covers/cover.webp');
});

it('deletes the cover and gallery files with the project', function (): void {
    Storage::disk('public')->put('projects/covers/cover.webp', 'cover');
    Storage::disk('public')->put('projects/gallery/one.webp', 'one');
    $project = Project::factory()->create(['cover_image' => 'projects/covers/cover.webp']);
    ProjectImage::factory()->for($project)->create(['path' => 'projects/gallery/one.webp']);

    $project->delete();

    Storage::disk('public')->assertMissing('projects/covers/cover.webp');
    Storage::disk('public')->assertMissing('projects/gallery/one.webp');
    expect(ProjectImage::query()->count())->toBe(0);
});

it('deletes a replaced CV from the private disk', function (): void {
    Storage::disk('local')->put('cv/old.pdf', 'old');
    $settings = SiteSetting::factory()->create(['cv_file' => 'cv/old.pdf']);

    $settings->update(['cv_file' => 'cv/new.pdf']);

    Storage::disk('local')->assertMissing('cv/old.pdf');
});
