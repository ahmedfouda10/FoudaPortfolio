<?php

declare(strict_types=1);

namespace App\Support;

/**
 * A square SVG with up to two initials: used for the favicon and the admin avatar,
 * so neither depends on a third-party image service.
 */
final class InitialsSvg
{
    public const string BACKGROUND = '#4F46E5';

    public static function make(string $name, int $size = 64): string
    {
        $initials = collect(preg_split('/\s+/', trim($name)) ?: [])
            ->map(fn (string $segment): string => mb_strtoupper(mb_substr((string) preg_replace('/^[^\p{L}\p{N}]+/u', '', $segment), 0, 1)))
            ->filter()
            ->take(2)
            ->join('');

        $text = htmlspecialchars($initials, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $radius = (int) round($size * 0.22);
        $fontSize = (int) round($size * 0.42);
        $background = self::BACKGROUND;

        return <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" width="{$size}" height="{$size}" viewBox="0 0 {$size} {$size}"><rect width="{$size}" height="{$size}" rx="{$radius}" fill="{$background}"/><text x="50%" y="50%" fill="#FFFFFF" font-family="ui-sans-serif, system-ui, sans-serif" font-size="{$fontSize}" font-weight="700" text-anchor="middle" dominant-baseline="central">{$text}</text></svg>
            SVG;
    }

    public static function dataUri(string $name, int $size = 64): string
    {
        return 'data:image/svg+xml;base64,'.base64_encode(self::make($name, $size));
    }
}
