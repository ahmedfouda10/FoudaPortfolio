<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Support\Images\ImageVariants;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;

/**
 * An <img> with a WebP `srcset` (when variants exist), intrinsic width/height
 * to prevent layout shift, and lazy loading unless it is above the fold.
 */
final class ResponsiveImage extends Component
{
    public readonly string $src;

    public readonly ?string $srcset;

    public readonly int $width;

    public readonly int $height;

    public function __construct(
        ImageVariants $variants,
        public string $path,
        public string $alt = '',
        public string $sizes = '100vw',
        public bool $priority = false,
        public string $disk = 'public',
        int $fallbackWidth = 1600,
        int $fallbackHeight = 900,
    ) {
        [$this->width, $this->height] = $variants->dimensions($disk, $path) ?? [$fallbackWidth, $fallbackHeight];

        $this->src = Storage::disk($disk)->url($path);
        $this->srcset = $variants->srcset($disk, $path, $this->width);
    }

    public function render(): View
    {
        return view('components.responsive-image');
    }
}
