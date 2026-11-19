<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Support\Images\ImageVariants;
use Illuminate\Support\Facades\Storage;

/**
 * Removes uploaded files (and their resized variants) from disk when they are
 * replaced, cleared or when the record is deleted, so storage never fills up
 * with orphans.
 */
trait DeletesReplacedFiles
{
    /**
     * Attributes holding a file path, mapped to the disk the file lives on.
     *
     * @return array<string, string>
     */
    abstract protected function fileAttributes(): array;

    protected static function bootDeletesReplacedFiles(): void
    {
        static::updated(function (self $model): void {
            foreach ($model->fileAttributes() as $attribute => $disk) {
                if ($model->wasChanged($attribute)) {
                    self::deleteFile($disk, $model->getOriginal($attribute));
                }
            }
        });

        static::deleted(function (self $model): void {
            foreach ($model->fileAttributes() as $attribute => $disk) {
                self::deleteFile($disk, $model->getAttribute($attribute));
            }
        });
    }

    private static function deleteFile(string $disk, mixed $path): void
    {
        if (is_string($path) && $path !== '') {
            Storage::disk($disk)->delete($path);
            app(ImageVariants::class)->delete($disk, $path);
        }
    }
}
