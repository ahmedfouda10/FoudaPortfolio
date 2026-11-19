<?php

declare(strict_types=1);

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\ProjectImage;

it('returns only published projects from published()', function (): void {
    $published = Project::factory()->create();
    Project::factory()->unpublished()->create();

    expect(Project::query()->published()->pluck('id')->all())->toBe([$published->id]);
});

it('returns only featured projects from featured()', function (): void {
    $featured = Project::factory()->featured()->create();
    Project::factory()->create();

    expect(Project::query()->featured()->pluck('id')->all())->toBe([$featured->id]);
});

it('lists featured projects first, then follows the manual order', function (): void {
    $regularFirst = Project::factory()->create(['sort_order' => 1]);
    $featuredLater = Project::factory()->featured()->create(['sort_order' => 5]);
    $regularSecond = Project::factory()->create(['sort_order' => 2]);
    $featuredEarlier = Project::factory()->featured()->create(['sort_order' => 4]);

    expect(Project::query()->featuredFirst()->pluck('id')->all())->toBe([
        $featuredEarlier->id,
        $featuredLater->id,
        $regularFirst->id,
        $regularSecond->id,
    ]);
});

it('returns gallery images in their manual order', function (): void {
    $project = Project::factory()->create();
    $second = ProjectImage::factory()->for($project)->create(['sort_order' => 2]);
    $first = ProjectImage::factory()->for($project)->create(['sort_order' => 1]);

    expect($project->images()->pluck('id')->all())->toBe([$first->id, $second->id]);
});

it('casts the status, tags and links', function (): void {
    $project = Project::factory()->create([
        'status' => ProjectStatus::InProgress,
        'integrations' => ['Shopify', 'Salla'],
        'links' => [['label' => 'Website', 'url' => 'https://example.com', 'type' => 'website']],
    ])->fresh();

    expect($project->status)->toBe(ProjectStatus::InProgress)
        ->and($project->integrations)->toBe(['Shopify', 'Salla'])
        // MySQL's JSON type normalizes key order, so compare values, not key order.
        ->and($project->links)->toEqual([['label' => 'Website', 'url' => 'https://example.com', 'type' => 'website']])
        ->and($project->views_count)->toBe(0);
});
