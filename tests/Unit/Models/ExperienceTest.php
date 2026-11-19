<?php

declare(strict_types=1);

use App\Models\Experience;

it('treats a position without an end date as the current one', function (): void {
    expect(Experience::factory()->current()->make()->isCurrent())->toBeTrue()
        ->and(Experience::factory()->make()->isCurrent())->toBeFalse();
});
