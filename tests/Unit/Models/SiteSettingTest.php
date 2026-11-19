<?php

declare(strict_types=1);

use App\Models\SiteSetting;

it('creates the settings row with safe defaults when it is missing', function (): void {
    config(['app.name' => 'My Portfolio']);

    $settings = SiteSetting::current();

    expect($settings->exists)->toBeTrue()
        ->and($settings->full_name)->toBe('My Portfolio')
        ->and(SiteSetting::query()->count())->toBe(1);
});

it('always returns the same single row', function (): void {
    $existing = SiteSetting::factory()->create();

    expect(SiteSetting::current()->is($existing))->toBeTrue()
        ->and(SiteSetting::current()->is($existing))->toBeTrue()
        ->and(SiteSetting::query()->count())->toBe(1);
});
