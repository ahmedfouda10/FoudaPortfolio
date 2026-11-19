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
use Illuminate\Support\Facades\DB;

function seedVisibleContent(): void
{
    SiteSetting::factory()->create([
        'full_name' => 'Ahmed Fouda',
        'headline' => 'Senior Backend Developer',
        'about_body' => '<p>I build Laravel backends.</p>',
        'about_highlights' => ['Shopify apps'],
        'years_of_experience' => 4,
        'projects_shipped' => null,
        'platforms_integrated' => null,
    ]);
    Experience::factory()->current()->create(['role' => 'Backend Developer', 'company' => 'Croco IT']);
    SkillGroup::factory()->has(Skill::factory()->state(['name' => 'Laravel']))->create(['name' => 'Backend']);
    Service::factory()->create(['title' => 'Shopify App Development']);
    Project::factory()->featured()->create(['title' => '2Loyal', 'slug' => '2loyal', 'integrations' => ['Shopify', 'Salla']]);
    Education::factory()->create(['degree' => 'B.Sc. in Computers and Information']);
    Certification::factory()->inProgress()->create(['title' => 'Claude learning path']);
}

it('renders every section of the one-page site', function (): void {
    seedVisibleContent();

    $response = $this->get('/')->assertOk();

    foreach (['top', 'about', 'experience', 'skills', 'services', 'projects', 'education', 'contact'] as $section) {
        $response->assertSee("id=\"{$section}\"", escape: false);
    }

    $response->assertSeeInOrder([
        'Ahmed Fouda', 'Senior Backend Developer',
        'I build Laravel backends.', 'Shopify apps',
        'Backend Developer', 'Croco IT', __('site.experience.present'),
        'Backend', 'Laravel',
        'Shopify App Development',
        '2Loyal',
        'B.Sc. in Computers and Information', 'Claude learning path',
    ]);
});

it('has exactly one h1 and a navigation link per section', function (): void {
    seedVisibleContent();

    $html = $this->get('/')->getContent();

    expect(substr_count((string) $html, '<h1'))->toBe(1);

    foreach (['about', 'experience', 'skills', 'services', 'projects', 'education'] as $section) {
        expect($html)->toContain("href=\"#{$section}\"");
    }
});

it('never renders hidden or unpublished content', function (): void {
    seedVisibleContent();

    Experience::factory()->hidden()->create(['role' => 'Hidden Role']);
    SkillGroup::factory()->hidden()->create(['name' => 'Hidden Group']);
    Skill::factory()->hidden()->for(SkillGroup::query()->firstOrFail(), 'group')->create(['name' => 'Hidden Skill']);
    Service::factory()->hidden()->create(['title' => 'Hidden Service']);
    Project::factory()->unpublished()->create(['title' => 'Secret Project']);
    Education::factory()->hidden()->create(['degree' => 'Hidden Degree']);
    Certification::factory()->hidden()->create(['title' => 'Hidden Certificate']);

    $this->get('/')
        ->assertOk()
        ->assertDontSee('Hidden Role')
        ->assertDontSee('Hidden Group')
        ->assertDontSee('Hidden Skill')
        ->assertDontSee('Hidden Service')
        ->assertDontSee('Secret Project')
        ->assertDontSee('Hidden Degree')
        ->assertDontSee('Hidden Certificate');
});

it('leaves out sections that have nothing to show, including their nav links', function (): void {
    SiteSetting::factory()->create(['about_body' => null, 'about_highlights' => null]);
    Service::factory()->hidden()->create();

    $html = (string) $this->get('/')->assertOk()->getContent();

    expect($html)
        ->not->toContain('id="services"')
        ->not->toContain('href="#services"')
        ->not->toContain('id="about"')
        ->toContain('id="contact"');
});

it('only shows the hero stats that are filled in', function (): void {
    SiteSetting::factory()->create(['years_of_experience' => 4, 'projects_shipped' => null, 'platforms_integrated' => 6]);

    $this->get('/')
        ->assertSee('4+')
        ->assertSee(__('site.hero.years_of_experience'))
        ->assertDontSee(__('site.hero.projects_shipped'))
        ->assertSee(__('site.hero.platforms_integrated'));
});

it('offers the CV download only when a CV has been uploaded', function (): void {
    $settings = SiteSetting::factory()->create(['cv_file' => null]);

    $this->get('/')->assertDontSee(route('cv.download'));

    $settings->update(['cv_file' => 'cv/resume.pdf']);

    $this->get('/')->assertSee(route('cv.download'));
});

it('lists featured projects first', function (): void {
    SiteSetting::factory()->create();
    Project::factory()->create(['title' => 'Regular Project', 'sort_order' => 1]);
    Project::factory()->featured()->create(['title' => 'Featured Project', 'sort_order' => 2]);

    $this->get('/')->assertSeeInOrder(['Featured Project', 'Regular Project']);
});

it('sanitizes rich text written in the dashboard', function (): void {
    SiteSetting::factory()->create(['about_body' => '<p>Hello</p><script>alert("x")</script><img src=x onerror="alert(1)">']);

    $this->get('/')
        ->assertSee('<p>Hello</p>', escape: false)
        ->assertDontSee('<script>alert("x")</script>', escape: false)
        ->assertDontSee('onerror', escape: false);
});

it('applies the theme before first paint and defaults to dark', function (): void {
    SiteSetting::factory()->create();

    $html = (string) $this->get('/')->getContent();

    expect($html)->toContain('<html lang="en" class="dark">')
        ->and(strpos($html, "localStorage.getItem('theme')"))->toBeLessThan(strpos($html, '/build/assets/app-') ?: PHP_INT_MAX);
});

it('renders the home page with a constant number of queries (no N+1)', function (): void {
    SiteSetting::factory()->create();

    $countQueries = function (): int {
        app(App\Support\PublicContentCache::class)->flush();
        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->get('/')->assertOk();

        return count(DB::getQueryLog());
    };

    SkillGroup::factory()->has(Skill::factory()->count(2))->create();
    Project::factory()->create();
    $baseline = $countQueries();

    SkillGroup::factory()->count(5)->has(Skill::factory()->count(5))->create();
    Project::factory()->count(5)->create();
    Experience::factory()->count(5)->create();

    expect($countQueries())->toBe($baseline);
});
