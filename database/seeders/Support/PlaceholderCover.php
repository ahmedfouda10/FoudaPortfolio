<?php

declare(strict_types=1);

namespace Database\Seeders\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Generates a neutral, clearly replaceable SVG cover (solid accent background
 * and the project name) so seeded projects never show a broken image.
 */
final class PlaceholderCover
{
    private const string BACKGROUND = '#4F46E5';

    private const int WIDTH = 1600;

    private const int HEIGHT = 900;

    /**
     * Store the placeholder on the public disk and return its path.
     */
    public static function store(string $slug, string $title): string
    {
        $path = "projects/covers/{$slug}-placeholder.svg";

        Storage::disk('public')->put($path, self::svg($title));

        return $path;
    }

    public static function svg(string $title): string
    {
        $title = htmlspecialchars($title, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $width = self::WIDTH;
        $height = self::HEIGHT;
        $centerX = $width / 2;
        $centerY = $height / 2;
        $footerY = $height - 60;
        $background = self::BACKGROUND;

        return <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 {$width} {$height}" role="img" aria-label="{$title}">
              <rect width="{$width}" height="{$height}" fill="{$background}"/>
              <text x="{$centerX}" y="{$centerY}" fill="#FFFFFF" font-family="Inter, ui-sans-serif, system-ui, sans-serif" font-size="112" font-weight="700" text-anchor="middle" dominant-baseline="middle">{$title}</text>
              <text x="{$centerX}" y="{$footerY}" fill="#FFFFFF" fill-opacity="0.65" font-family="ui-monospace, SFMono-Regular, Menlo, monospace" font-size="28" text-anchor="middle">placeholder cover · replace it from the dashboard</text>
            </svg>
            SVG;
    }
}
