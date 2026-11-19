<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\SiteSetting;
use App\Support\Images\ImageVariants;
use App\Support\PublicContentCache;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * (Re)builds the responsive WebP variants of every stored image, e.g. after
 * seeding, after a deploy to a new server or after changing the widths.
 */
#[Signature('images:variants')]
#[Description('Generate responsive WebP variants for all uploaded images')]
final class GenerateImageVariantsCommand extends Command
{
    public function handle(ImageVariants $variants, PublicContentCache $cache): int
    {
        $paths = collect()
            ->merge(Project::query()->whereNotNull('cover_image')->pluck('cover_image'))
            ->merge(ProjectImage::query()->pluck('path'))
            ->merge(SiteSetting::query()->whereNotNull('avatar')->pluck('avatar'))
            ->filter(fn (mixed $path): bool => is_string($path) && $variants->supports($path))
            ->unique();

        $count = 0;

        foreach ($paths as $path) {
            $count += count($variants->generate('public', $path));
        }

        $cache->flush();

        $this->components->info("Generated {$count} variants for {$paths->count()} images.");

        return self::SUCCESS;
    }
}
