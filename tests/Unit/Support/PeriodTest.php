<?php

declare(strict_types=1);

use App\Support\Period;
use Carbon\CarbonImmutable;

it('formats month-precision periods', function (?string $from, ?string $to, bool $ongoing, string $expected): void {
    expect(Period::format(
        $from === null ? null : CarbonImmutable::parse($from),
        $to === null ? null : CarbonImmutable::parse($to),
        $ongoing,
    ))->toBe($expected);
})->with([
    'closed range' => ['2022-06-01', '2023-02-01', false, 'Jun 2022 – Feb 2023'],
    'open range' => ['2023-02-01', null, false, 'Feb 2023 – Present'],
    'single month' => [null, '2022-01-01', false, 'Jan 2022'],
    'same month' => ['2022-01-01', '2022-01-20', false, 'Jan 2022'],
    'ongoing without dates' => [null, null, true, 'Present'],
    'no dates at all' => [null, null, false, ''],
]);
