<?php

declare(strict_types=1);

use App\Data\HomePageContent;
use App\Enums\ProjectStatus;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Queries\GetHomePageContent;
use App\Support\PublicContentCache;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

beforeEach(fn () => SiteSetting::factory()->create(['headline' => 'Original headline']));

it('serves the home page from the cache', function (): void {
    Project::factory()->create();
    $this->get('/')->assertOk();

    DB::flushQueryLog();
    DB::enableQueryLog();

    $this->get('/')->assertOk();

    expect(DB::getQueryLog())->toBe([]);
});

it('refreshes the home page when a project changes', function (): void {
    $project = Project::factory()->create(['title' => 'Old title']);
    $this->get('/')->assertSee('Old title');

    $project->update(['title' => 'New title']);

    $this->get('/')->assertSee('New title')->assertDontSee('Old title');
});

it('refreshes the home page when the settings change', function (): void {
    $this->get('/')->assertSee('Original headline');

    SiteSetting::current()->update(['headline' => 'Updated headline']);

    $this->get('/')->assertSee('Updated headline');
});

it('refreshes the project page when its project is deleted', function (): void {
    $project = Project::factory()->create();
    $this->get(route('projects.show', $project->slug))->assertOk();

    $project->delete();

    $this->get(route('projects.show', $project->slug))->assertNotFound();
});

it('refreshes the home page after drag-and-drop reordering in the dashboard', function (): void {
    actingAsAdmin();
    $first = Project::factory()->create(['title' => 'Alpha', 'sort_order' => 1]);
    $second = Project::factory()->create(['title' => 'Beta', 'sort_order' => 2]);
    $this->get('/')->assertSeeInOrder(['Alpha', 'Beta']);

    Livewire::test(ListProjects::class)->call('reorderTable', [(string) $second->id, (string) $first->id]);

    $this->get('/')->assertSeeInOrder(['Beta', 'Alpha']);
});

it('is not invalidated by view counter updates', function (): void {
    $project = Project::factory()->create();
    $cache = app(PublicContentCache::class);
    $key = $cache->key('home');

    $project->forceFill(['views_count' => 10])->save();

    expect($cache->key('home'))->toBe($key);
});

it('survives a real round-trip through a serializing cache store', function (): void {
    config(['cache.default' => 'database']);
    app()->forgetInstance('cache.store');
    Cache::clearResolvedInstances();

    Project::factory()->create(['status' => ProjectStatus::Live]);

    app(GetHomePageContent::class)();          // stores
    $content = app(GetHomePageContent::class)(); // reads back through unserialize()

    expect($content)->toBeInstanceOf(HomePageContent::class)
        ->and($content->settings)->toBeInstanceOf(SiteSetting::class)
        ->and($content->projects->first())->toBeInstanceOf(Project::class)
        ->and($content->projects->first()->status)->toBe(ProjectStatus::Live)
        ->and($content->settings->created_at)->toBeInstanceOf(CarbonImmutable::class);
});
