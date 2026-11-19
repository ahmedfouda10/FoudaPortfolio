<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\CvDownload;
use Carbon\CarbonImmutable;

/**
 * Daily CV download counts for the last N days (oldest first), with zero-filled gaps.
 */
final class CvDownloadsPerDay
{
    /**
     * @return array<string, int> Keyed by Y-m-d.
     */
    public function __invoke(int $days = 30): array
    {
        $today = CarbonImmutable::today();
        $from = $today->subDays($days - 1);

        /** @var array<string, int|string> $counts */
        $counts = CvDownload::query()
            ->where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as aggregate')
            ->groupBy('day')
            ->pluck('aggregate', 'day')
            ->all();

        $series = [];

        for ($date = $from; $date->lte($today); $date = $date->addDay()) {
            $series[$date->toDateString()] = (int) ($counts[$date->toDateString()] ?? 0);
        }

        return $series;
    }
}
