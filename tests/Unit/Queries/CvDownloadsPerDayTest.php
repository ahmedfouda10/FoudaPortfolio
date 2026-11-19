<?php

declare(strict_types=1);

use App\Models\CvDownload;
use App\Queries\CvDownloadsPerDay;

it('returns zero-filled daily counts for the requested window', function (): void {
    $this->travelTo(now()->setDate(2026, 11, 19)->setTime(12, 0));

    CvDownload::factory()->count(2)->create(['created_at' => now()->subDays(2)]);
    CvDownload::factory()->create(['created_at' => now()]);
    CvDownload::factory()->create(['created_at' => now()->subDays(7)]); // outside a 7-day window

    $series = (new CvDownloadsPerDay)(7);

    expect($series)->toHaveCount(7)
        ->and(array_key_first($series))->toBe('2026-11-13')
        ->and(array_key_last($series))->toBe('2026-11-19')
        ->and($series['2026-11-17'])->toBe(2)
        ->and($series['2026-11-19'])->toBe(1)
        ->and(array_sum($series))->toBe(3);
});
