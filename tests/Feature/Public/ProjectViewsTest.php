<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\SiteSetting;
use App\Support\PublicContentCache;

beforeEach(fn () => SiteSetting::factory()->create());

const BROWSER = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0 Safari/537.36';

it('counts a view once per visitor per day', function (): void {
    $project = Project::factory()->create();

    $this->withHeader('User-Agent', BROWSER)->get(route('projects.show', $project->slug))->assertOk();
    $this->withHeader('User-Agent', BROWSER)->get(route('projects.show', $project->slug))->assertOk();

    expect($project->refresh()->views_count)->toBe(1);

    $this->travel(1)->day();
    $this->withHeader('User-Agent', BROWSER)->get(route('projects.show', $project->slug));

    expect($project->refresh()->views_count)->toBe(2);
});

it('counts each visitor separately', function (): void {
    $project = Project::factory()->create();

    $this->withHeader('User-Agent', BROWSER)->get(route('projects.show', $project->slug));
    $this->flushSession();
    $this->withHeader('User-Agent', BROWSER)->get(route('projects.show', $project->slug));

    expect($project->refresh()->views_count)->toBe(2);
});

it('ignores bots and link previews', function (string $userAgent): void {
    $project = Project::factory()->create();

    $this->withHeader('User-Agent', $userAgent)->get(route('projects.show', $project->slug))->assertOk();

    expect($project->refresh()->views_count)->toBe(0);
})->with([
    'Googlebot' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
    'LinkedIn preview' => 'LinkedInBot/1.0 (compatible; Mozilla/5.0; Apache-HttpClient +http://www.linkedin.com)',
    'Lighthouse' => 'Mozilla/5.0 (Linux; Android 11) Chrome-Lighthouse',
    'empty' => '',
]);

it('does not count unpublished projects', function (): void {
    $project = Project::factory()->unpublished()->create();

    $this->withHeader('User-Agent', BROWSER)->get(route('projects.show', $project->slug))->assertNotFound();

    expect($project->refresh()->views_count)->toBe(0);
});

it('does not invalidate the cached public pages', function (): void {
    $project = Project::factory()->create();
    $key = app(PublicContentCache::class)->key('home');

    $this->withHeader('User-Agent', BROWSER)->get(route('projects.show', $project->slug));

    expect(app(PublicContentCache::class)->key('home'))->toBe($key);
});
