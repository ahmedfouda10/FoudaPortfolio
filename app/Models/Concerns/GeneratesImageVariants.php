<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Jobs\GenerateImageVariants;
use App\Support\Images\ImageVariants;

/**
 * Queues the resized WebP variants of image attributes whenever a new image
 * is stored. The variants are removed together with the original file by
 * DeletesReplacedFiles.
 */
trait GeneratesImageVariants
{
    /**
     * Image attributes that get responsive variants, mapped to their disk.
     *
     * @return array<string, string>
     */
    abstract protected function responsiveImageAttributes(): array;

    protected static function bootGeneratesImageVariants(): void
    {
        static::created(function (self $model): void {
            foreach ($model->responsiveImageAttributes() as $attribute => $disk) {
                self::queueVariants($disk, $model->getAttribute($attribute));
            }
        });

        static::updated(function (self $model): void {
            foreach ($model->responsiveImageAttributes() as $attribute => $disk) {
                if ($model->wasChanged($attribute)) {
                    self::queueVariants($disk, $model->getAttribute($attribute));
                }
            }
        });
    }

    private static function queueVariants(string $disk, mixed $path): void
    {
        if (is_string($path) && $path !== '' && app(ImageVariants::class)->supports($path)) {
            GenerateImageVariants::dispatch($disk, $path);
        }
    }
}
