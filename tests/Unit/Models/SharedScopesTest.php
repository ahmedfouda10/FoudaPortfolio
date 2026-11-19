<?php

declare(strict_types=1);

use App\Models\Certification;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\SkillGroup;

dataset('models with visibility', [
    'experience' => Experience::class,
    'skill group' => SkillGroup::class,
    'skill' => Skill::class,
    'service' => Service::class,
    'education' => Education::class,
    'certification' => Certification::class,
]);

dataset('models with sort order', [
    'experience' => Experience::class,
    'skill group' => SkillGroup::class,
    'skill' => Skill::class,
    'service' => Service::class,
    'project' => Project::class,
    'education' => Education::class,
    'certification' => Certification::class,
]);

it('returns only visible records from visible()', function (string $model): void {
    $shown = $model::factory()->create();
    $model::factory()->hidden()->create();

    expect($model::query()->visible()->pluck('id')->all())->toBe([$shown->id]);
})->with('models with visibility');

it('orders records by sort_order and then by id with ordered()', function (string $model): void {
    $last = $model::factory()->create(['sort_order' => 3]);
    $first = $model::factory()->create(['sort_order' => 1]);
    $secondA = $model::factory()->create(['sort_order' => 2]);
    $secondB = $model::factory()->create(['sort_order' => 2]);

    expect($model::query()->ordered()->pluck('id')->all())
        ->toBe([$first->id, $secondA->id, $secondB->id, $last->id]);
})->with('models with sort order');
