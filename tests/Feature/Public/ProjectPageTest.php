<?php

declare(strict_types=1);

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\SiteSetting;

beforeEach(fn () => SiteSetting::factory()->create(['full_name' => 'Ahmed Fouda']));

it('shows a published case study', function (): void {
    $project = Project::factory()->create([
        'title' => '2Loyal',
        'slug' => '2loyal',
        'status' => ProjectStatus::Live,
        'problem' => 'Merchants lose customers after the first purchase.',
        'my_role' => 'Designed and built the product end-to-end.',
        'solution' => '<ul><li>RFM customer segmentation</li></ul>',
        'results' => '<p>Approved on the Shopify App Store.</p>',
        'integrations' => ['Shopify', 'Salla'],
        'tech_stack' => ['Laravel', 'MySQL'],
        'links' => [['label' => 'Shopify App Store', 'url' => 'https://apps.shopify.com/2loyal', 'type' => 'shopify_app']],
    ]);

    $this->get(route('projects.show', $project->slug))
        ->assertOk()
        ->assertSee('<title>2Loyal — Ahmed Fouda</title>', escape: false)
        ->assertSeeInOrder([
            __('site.project.problem'), 'Merchants lose customers after the first purchase.',
            __('site.project.my_role'), 'Designed and built the product end-to-end.',
            __('site.project.solution'), 'RFM customer segmentation',
            __('site.project.integrations'), 'Shopify', 'Salla',
            __('site.project.results'), 'Approved on the Shopify App Store.',
            __('site.project.facts'), 'Laravel', 'MySQL',
        ])
        ->assertSee('https://apps.shopify.com/2loyal');
});

it('returns 404 for unpublished projects', function (): void {
    $project = Project::factory()->unpublished()->create();

    $this->get(route('projects.show', $project->slug))->assertNotFound();
});

it('returns 404 for unknown projects', function (): void {
    $this->get('/projects/does-not-exist')->assertNotFound();
});

it('links to the next published project and wraps around', function (): void {
    $first = Project::factory()->create(['title' => 'First', 'sort_order' => 1]);
    Project::factory()->unpublished()->create(['title' => 'Draft', 'sort_order' => 2]);
    $last = Project::factory()->create(['title' => 'Last', 'sort_order' => 3]);

    $this->get(route('projects.show', $first->slug))->assertSee(route('projects.show', $last->slug));
    $this->get(route('projects.show', $last->slug))->assertSee(route('projects.show', $first->slug));
});

it('renders the gallery with alt text for the lightbox', function (): void {
    $project = Project::factory()->create();
    ProjectImage::factory()->for($project)->create(['alt' => 'Loyalty dashboard', 'caption' => 'Points overview']);

    $this->get(route('projects.show', $project->slug))
        ->assertOk()
        ->assertSee(__('site.project.gallery'))
        ->assertSee(__('site.project.open_image', ['alt' => 'Loyalty dashboard']))
        ->assertSee('role="dialog"', escape: false);
});
