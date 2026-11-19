<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\SiteSetting;

beforeEach(fn () => SiteSetting::factory()->create(['full_name' => 'Ahmed Fouda']));

it('lists the home page and published projects in the sitemap', function (): void {
    $published = Project::factory()->create(['slug' => 'published-project']);
    Project::factory()->unpublished()->create(['slug' => 'draft-project']);

    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
        ->assertSee('<loc>'.route('home').'</loc>', escape: false)
        ->assertSee('<loc>'.route('projects.show', $published->slug).'</loc>', escape: false)
        ->assertDontSee('draft-project');
});

it('lets crawlers index production but keeps them out of the dashboard', function (): void {
    app()->detectEnvironment(fn (): string => 'production');

    $this->get('/robots.txt')
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee('Disallow: /admin')
        ->assertSee('Sitemap: '.route('sitemap'))
        ->assertDontSee("Disallow: /\n", escape: false);
});

it('blocks all crawlers outside production', function (): void {
    expect((string) $this->get('/robots.txt')->getContent())->toBe("User-agent: *\nDisallow: /\n");
});

it('serves a favicon with the owner’s initials', function (): void {
    $this->get('/favicon.svg')
        ->assertOk()
        ->assertHeader('Content-Type', 'image/svg+xml')
        ->assertSee('>AF</text>', escape: false);
});

it('sends baseline security headers on public pages and the dashboard', function (string $url): void {
    $this->get($url)
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
})->with(['/', '/admin/login']);

it('renders a friendly 404 page', function (): void {
    $this->get('/projects/missing')
        ->assertNotFound()
        ->assertSee(__('site.errors.404.title'))
        ->assertSee(__('site.errors.back_home'));
});

it('renders the 500 page without touching the database', function (): void {
    Illuminate\Support\Facades\DB::enableQueryLog();

    $html = view('errors.500')->render();

    expect($html)->toContain(__('site.errors.500.title'))
        ->and(Illuminate\Support\Facades\DB::getQueryLog())->toBe([]);
});
