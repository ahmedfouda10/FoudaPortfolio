<?php

declare(strict_types=1);

namespace App\Filament\Support;

use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

/**
 * Outline Heroicons as select options, keyed by their Blade Icons name
 * (e.g. "heroicon-o-sparkles") so the public site can render them with svg().
 */
final class HeroiconOptions
{
    /**
     * @return array<string, string>
     */
    public static function outline(): array
    {
        $options = [];

        foreach (Heroicon::cases() as $icon) {
            if (! str_starts_with($icon->value, 'o-')) {
                continue;
            }

            $options["heroicon-{$icon->value}"] = Str::of($icon->value)->after('o-')->replace('-', ' ')->ucfirst()->toString();
        }

        return $options;
    }
}
