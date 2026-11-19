<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\SkillGroup;

/**
 * @return list<array<string, mixed>>
 */
function jsonLd(string $html): array
{
    preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $matches);

    return array_map(fn (string $json): array => json_decode($json, true, flags: JSON_THROW_ON_ERROR), $matches[1]);
}

it('renders title, description, canonical, Open Graph and Twitter tags on the home page', function (): void {
    SiteSetting::factory()->create([
        'full_name' => 'Ahmed Fouda',
        'meta_title' => 'Ahmed Fouda — Senior Backend Developer',
        'meta_description' => 'Laravel backends and e-commerce integrations.',
        'og_image' => 'seo/share.png',
    ]);

    $this->get('/?inquiry=freelance_project')
        ->assertOk()
        ->assertSee('<title>Ahmed Fouda — Senior Backend Developer</title>', escape: false)
        ->assertSee('<meta name="description" content="Laravel backends and e-commerce integrations.">', escape: false)
        ->assertSee('<link rel="canonical" href="'.route('home').'">', escape: false)
        ->assertSee('<meta property="og:type" content="website">', escape: false)
        ->assertSee('<meta property="og:image" content="'.url('storage/seo/share.png').'">', escape: false)
        ->assertSee('<meta name="twitter:card" content="summary_large_image">', escape: false);
});

it('falls back to the profile photo as the share image, and never to an SVG', function (): void {
    SiteSetting::factory()->create(['og_image' => null, 'avatar' => 'profile/me.jpg']);
    $project = Project::factory()->create(['cover_image' => 'projects/covers/placeholder.svg']);

    $this->get('/')->assertSee('<meta property="og:image" content="'.url('storage/profile/me.jpg').'">', escape: false);

    $this->get(route('projects.show', $project->slug))
        ->assertSee('<meta property="og:type" content="article">', escape: false)
        ->assertSee('<meta property="og:image" content="'.url('storage/profile/me.jpg').'">', escape: false)
        ->assertDontSee('placeholder.svg">', escape: false);
});

it('uses the project cover as the share image of its case study', function (): void {
    SiteSetting::factory()->create();
    $project = Project::factory()->create(['cover_image' => 'projects/covers/cover.webp', 'cover_image_alt' => 'Dashboard screenshot']);

    $this->get(route('projects.show', $project->slug))
        ->assertSee('<meta property="og:image" content="'.url('storage/projects/covers/cover.webp').'">', escape: false)
        ->assertSee('<meta property="og:image:alt" content="Dashboard screenshot">', escape: false);
});

it('describes the owner as a schema.org Person on the home page', function (): void {
    SiteSetting::factory()->create([
        'full_name' => 'Ahmed Fouda',
        'headline' => 'Senior Backend Developer',
        'linkedin_url' => 'https://www.linkedin.com/in/example',
        'github_url' => 'https://github.com/example',
        'freelancer_url' => null,
    ]);
    SkillGroup::factory()->has(Skill::factory()->state(['name' => 'Laravel']))->create();

    $graph = collect(jsonLd((string) $this->get('/')->getContent()));
    $person = $graph->firstWhere('@type', 'Person');

    expect($person)
        ->name->toBe('Ahmed Fouda')
        ->jobTitle->toBe('Senior Backend Developer')
        ->sameAs->toBe(['https://www.linkedin.com/in/example', 'https://github.com/example'])
        ->knowsAbout->toBe(['Laravel'])
        ->and($graph->firstWhere('@type', 'WebSite'))->not->toBeNull();
});

it('describes a project as a schema.org CreativeWork with breadcrumbs', function (): void {
    SiteSetting::factory()->create(['full_name' => 'Ahmed Fouda']);
    $project = Project::factory()->create([
        'title' => '2Loyal',
        'year' => 2026,
        'integrations' => ['Shopify'],
        'tech_stack' => ['Laravel'],
        'links' => [['label' => 'Website', 'url' => 'https://2loyal.net/', 'type' => 'website']],
    ]);

    $graph = collect(jsonLd((string) $this->get(route('projects.show', $project->slug))->getContent()));

    expect($graph->firstWhere('@type', 'CreativeWork'))
        ->name->toBe('2Loyal')
        ->dateCreated->toBe('2026')
        ->keywords->toBe('Shopify, Laravel')
        ->sameAs->toBe(['https://2loyal.net/'])
        ->and($graph->firstWhere('@type', 'CreativeWork')['creator']['name'])->toBe('Ahmed Fouda')
        ->and($graph->firstWhere('@type', 'BreadcrumbList')['itemListElement'])->toHaveCount(3);
});

it('cannot break out of the JSON-LD script tag', function (): void {
    SiteSetting::factory()->create(['headline' => '</script><script>alert(1)</script>']);

    expect((string) $this->get('/')->getContent())->not->toContain('</script><script>alert(1)');
});
