<?php

declare(strict_types=1);

use App\Jobs\GenerateImageVariants;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Support\Images\ImageVariants;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('public'));

function storeFakeImage(string $path, int $width, int $height): void
{
    Storage::disk('public')->put($path, UploadedFile::fake()->image('image.png', $width, $height)->getContent());
}

it('creates WebP variants narrower than the original only', function (): void {
    storeFakeImage('projects/covers/cover.png', 1200, 675);

    $generated = app(ImageVariants::class)->generate('public', 'projects/covers/cover.png');

    expect($generated)->toBe([
        'projects/covers/variants/cover-480.webp',
        'projects/covers/variants/cover-960.webp',
    ]);

    [$width, $height, $type] = getimagesizefromstring((string) Storage::disk('public')->get('projects/covers/variants/cover-480.webp'));

    expect([$width, $height, $type])->toBe([480, 270, IMAGETYPE_WEBP]);
});

it('skips SVG files and missing files', function (): void {
    Storage::disk('public')->put('projects/covers/placeholder.svg', '<svg xmlns="http://www.w3.org/2000/svg"/>');

    expect(app(ImageVariants::class)->generate('public', 'projects/covers/placeholder.svg'))->toBe([])
        ->and(app(ImageVariants::class)->generate('public', 'projects/covers/missing.png'))->toBe([]);
});

it('builds a srcset from the existing variants', function (): void {
    storeFakeImage('projects/covers/cover.png', 1200, 675);
    app(ImageVariants::class)->generate('public', 'projects/covers/cover.png');

    $disk = Storage::disk('public');

    expect(app(ImageVariants::class)->srcset('public', 'projects/covers/cover.png', 1200))->toBe(implode(', ', [
        $disk->url('projects/covers/variants/cover-480.webp').' 480w',
        $disk->url('projects/covers/variants/cover-960.webp').' 960w',
        $disk->url('projects/covers/cover.png').' 1200w',
    ]));
});

it('queues the variants when a new image is saved', function (): void {
    Queue::fake();

    $project = Project::factory()->create(['cover_image' => 'projects/covers/new.png']);
    Queue::assertPushed(GenerateImageVariants::class, fn (GenerateImageVariants $job): bool => $job->path === 'projects/covers/new.png');

    $project->update(['title' => 'Renamed']);
    Queue::assertPushed(GenerateImageVariants::class, 1);

    SiteSetting::factory()->create(['avatar' => 'profile/me.jpg']);
    Queue::assertPushed(GenerateImageVariants::class, fn (GenerateImageVariants $job): bool => $job->path === 'profile/me.jpg');
});

it('removes the variants together with the original', function (): void {
    storeFakeImage('projects/covers/cover.png', 1200, 675);
    $project = Project::factory()->create(['cover_image' => 'projects/covers/cover.png']);
    app(ImageVariants::class)->generate('public', 'projects/covers/cover.png');

    $project->delete();

    Storage::disk('public')->assertMissing('projects/covers/cover.png');
    Storage::disk('public')->assertMissing('projects/covers/variants/cover-480.webp');
    Storage::disk('public')->assertMissing('projects/covers/variants/cover-960.webp');
});

it('rebuilds every variant from the command', function (): void {
    storeFakeImage('projects/covers/cover.png', 1200, 675);
    Project::factory()->createQuietly(['cover_image' => 'projects/covers/cover.png']);

    $this->artisan('images:variants')->assertSuccessful();

    Storage::disk('public')->assertExists('projects/covers/variants/cover-960.webp');
});

it('renders a lazy responsive image with intrinsic dimensions', function (): void {
    storeFakeImage('projects/covers/cover.png', 1200, 675);
    app(ImageVariants::class)->generate('public', 'projects/covers/cover.png');

    $html = Blade::render('<x-responsive-image path="projects/covers/cover.png" alt="Cover" sizes="50vw" />');

    expect($html)
        ->toContain('width="1200"')
        ->toContain('height="675"')
        ->toContain('loading="lazy"')
        ->toContain('sizes="50vw"')
        ->toContain('cover-480.webp 480w');

    expect(Blade::render('<x-responsive-image path="projects/covers/cover.png" :priority="true" />'))
        ->toContain('fetchpriority="high"')
        ->not->toContain('loading="lazy"');
});
