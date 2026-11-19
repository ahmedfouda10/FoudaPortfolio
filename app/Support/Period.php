<?php

declare(strict_types=1);

namespace App\Support;

use Carbon\CarbonInterface;

/**
 * Formats month-precision periods such as "Feb 2023 – Present" or "Jan 2022".
 */
final class Period
{
    private const string FORMAT = 'M Y';

    public static function format(?CarbonInterface $from, ?CarbonInterface $to, bool $isOngoing = false): string
    {
        $end = match (true) {
            $isOngoing, $to === null && $from !== null => self::presentLabel(),
            $to !== null => $to->translatedFormat(self::FORMAT),
            default => null,
        };

        if ($from === null) {
            return $end ?? '';
        }

        $start = $from->translatedFormat(self::FORMAT);

        return $end === $start ? $start : "{$start} – {$end}";
    }

    private static function presentLabel(): string
    {
        $label = __('common.present');

        return is_string($label) ? $label : 'Present';
    }
}
