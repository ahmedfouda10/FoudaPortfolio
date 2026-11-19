<?php

declare(strict_types=1);

namespace App\Support\Images;

use GdImage;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Builds and serves resized WebP copies of uploaded images, so the site can
 * offer a `srcset` instead of sending a full-size upload to every device.
 *
 * Variants live next to the original: `projects/covers/variants/cover-960.webp`.
 */
final class ImageVariants
{
    /**
     * @var list<positive-int>
     */
    public const array WIDTHS = [480, 960, 1440];

    private const int QUALITY = 80;

    /**
     * @var list<string>
     */
    private const array RASTER_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    public function supports(string $path): bool
    {
        return in_array(mb_strtolower(pathinfo($path, PATHINFO_EXTENSION)), self::RASTER_EXTENSIONS, true);
    }

    /**
     * Creates one WebP per configured width that is smaller than the original.
     *
     * @return list<string> The paths of the generated variants.
     */
    public function generate(string $disk, string $path): array
    {
        $storage = Storage::disk($disk);

        if (! $this->supports($path) || ! $storage->exists($path)) {
            return [];
        }

        $source = @imagecreatefromstring((string) $storage->get($path));

        if (! $source instanceof GdImage) {
            return [];
        }

        $originalWidth = imagesx($source);
        $originalHeight = imagesy($source);
        $generated = [];

        foreach (self::WIDTHS as $width) {
            if ($width >= $originalWidth) {
                continue;
            }

            $height = max(1, (int) round($originalHeight * $width / $originalWidth));

            $generated[] = $this->store($storage, $this->variantPath($path, $width), $source, $width, $height);
        }

        imagedestroy($source);

        return $generated;
    }

    public function delete(string $disk, string $path): void
    {
        Storage::disk($disk)->delete(array_map(fn (int $width): string => $this->variantPath($path, $width), self::WIDTHS));
        Cache::forget("image-dimensions:{$disk}:{$path}");
    }

    /**
     * A `srcset` built from the variants that exist, plus the original as the largest candidate.
     */
    public function srcset(string $disk, string $path, int $originalWidth): ?string
    {
        if (! $this->supports($path)) {
            return null;
        }

        $storage = Storage::disk($disk);

        $candidates = collect(self::WIDTHS)
            ->filter(fn (int $width): bool => $width < $originalWidth && $storage->exists($this->variantPath($path, $width)))
            ->map(fn (int $width): string => $storage->url($this->variantPath($path, $width))." {$width}w");

        if ($candidates->isEmpty()) {
            return null;
        }

        return $candidates->push($storage->url($path)." {$originalWidth}w")->implode(', ');
    }

    /**
     * Intrinsic size of a stored raster image, cached because it never changes for a given path.
     *
     * @return array{0: int, 1: int}|null
     */
    public function dimensions(string $disk, string $path): ?array
    {
        if (! $this->supports($path)) {
            return null;
        }

        /** @var array{0: int, 1: int}|null $dimensions */
        $dimensions = Cache::rememberForever("image-dimensions:{$disk}:{$path}", function () use ($disk, $path): ?array {
            $storage = Storage::disk($disk);

            if (! $storage->exists($path)) {
                return null;
            }

            $size = @getimagesizefromstring((string) $storage->get($path));

            return $size === false ? null : [$size[0], $size[1]];
        });

        return $dimensions;
    }

    public function variantPath(string $path, int $width): string
    {
        $directory = pathinfo($path, PATHINFO_DIRNAME);
        $name = pathinfo($path, PATHINFO_FILENAME);

        return ltrim("{$directory}/variants/{$name}-{$width}.webp", './');
    }

    /**
     * @param  positive-int  $width
     * @param  positive-int  $height
     */
    private function store(Filesystem $storage, string $target, GdImage $source, int $width, int $height): string
    {
        $resized = imagecreatetruecolor($width, $height);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $source, 0, 0, 0, 0, $width, $height, imagesx($source), imagesy($source));

        ob_start();
        imagewebp($resized, null, self::QUALITY);
        $storage->put($target, (string) ob_get_clean());

        imagedestroy($resized);

        return $target;
    }
}
