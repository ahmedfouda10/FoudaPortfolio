<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Support\Images\ImageVariants;
use App\Support\PublicContentCache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class GenerateImageVariants implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $disk,
        public readonly string $path,
    ) {
        $this->afterCommit();
    }

    public function handle(ImageVariants $variants, PublicContentCache $cache): void
    {
        if ($variants->generate($this->disk, $this->path) !== []) {
            // The pages may have been cached before the variants existed.
            $cache->flush();
        }
    }
}
