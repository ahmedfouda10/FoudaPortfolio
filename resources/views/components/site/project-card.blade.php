@props(['project'])
<article class="card group relative flex h-full flex-col overflow-hidden transition-colors hover:border-accent/60">
    <div class="overflow-hidden border-b border-border bg-surface-muted">
        @if (filled($project->cover_image))
            <x-responsive-image
                :path="$project->cover_image"
                :alt="$project->cover_image_alt ?? ''"
                sizes="(min-width: 1152px) 544px, (min-width: 768px) 50vw, 100vw"
                class="aspect-video w-full object-cover object-top transition-transform duration-500 motion-safe:group-hover:scale-[1.02]"
            />
        @else
            <div class="flex aspect-video items-center justify-center font-mono text-2xl font-semibold text-accent" aria-hidden="true">{{ $project->title }}</div>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-6">
        <div class="flex flex-wrap items-center gap-2">
            <x-site.project-status :status="$project->status" />
            @if ($project->is_featured)
                <span class="badge badge-accent">{{ __('site.projects.featured') }}</span>
            @endif
            @if ($project->year)
                <span class="font-mono text-xs text-muted">{{ $project->year }}</span>
            @endif
        </div>

        <h3 class="mt-4 text-xl font-semibold">
            {{-- Stretched link: the whole card opens the case study. --}}
            <a href="{{ route('projects.show', $project->slug) }}" class="after:absolute after:inset-0 hover:text-accent">
                {{ $project->title }}
            </a>
        </h3>
        @if (filled($project->subtitle))
            <p class="mt-1 font-mono text-xs text-muted">{{ $project->subtitle }}</p>
        @endif
        @if (filled($project->tagline))
            <p class="mt-3 leading-relaxed text-muted">{{ $project->tagline }}</p>
        @endif

        @if (filled($project->integrations))
            <ul class="mt-4 flex flex-wrap gap-2" aria-label="{{ __('site.project.integrations') }}">
                @foreach ($project->integrations as $integration)
                    <li class="badge badge-accent">{{ $integration }}</li>
                @endforeach
            </ul>
        @endif

        @if (filled($project->tech_stack))
            <ul class="mt-3 flex flex-wrap gap-1.5" aria-label="{{ __('site.project.stack') }}">
                @foreach ($project->tech_stack as $tech)
                    <li class="chip">{{ $tech }}</li>
                @endforeach
            </ul>
        @endif

        <div class="relative z-10 mt-auto flex flex-wrap items-center gap-2 pt-6">
            <a href="{{ route('projects.show', $project->slug) }}" class="btn btn-primary">
                {{ __('site.projects.case_study') }}
                <span class="sr-only">: {{ $project->title }}</span>
            </a>
            @foreach ($project->links ?? [] as $link)
                <a
                    href="{{ $link['url'] }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn btn-secondary px-3"
                    title="{{ $link['label'] }}"
                >
                    <x-site.link-icon :type="$link['type'] ?? 'other'" class="size-4" />
                    <span class="sr-only">{{ $link['label'] }} {{ __('site.project.opens_in_new_tab') }}</span>
                </a>
            @endforeach
        </div>
    </div>
</article>
