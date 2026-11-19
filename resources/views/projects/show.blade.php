@php
    /** @var \App\Data\ProjectPageContent $page */
    $project = $page->project;
    $settings = $page->settings;
    $gallery = $project->images->map(fn ($image): array => [
        'path' => $image->path,
        'src' => Storage::disk('public')->url($image->path),
        'alt' => $image->alt ?? $project->title,
        'caption' => $image->caption,
    ])->values();
    $chapters = array_filter([
        'problem' => filled($project->problem) ? e($project->problem) : null,
        'my_role' => filled($project->my_role) ? e($project->my_role) : null,
        'solution' => filled($project->solution) ? Str::sanitizeHtml($project->solution) : null,
        'integrations' => null,
        'results' => filled($project->results) ? Str::sanitizeHtml($project->results) : null,
    ], fn (?string $html, string $key): bool => $html !== null || ($key === 'integrations' && filled($project->integrations)), ARRAY_FILTER_USE_BOTH);
@endphp
<x-layouts.public
    :settings="$settings"
    :seo="$seo"
    :sections="['about', 'experience', 'skills', 'services', 'projects', 'education', 'contact']"
>
    <article>
        <header class="relative isolate overflow-hidden border-b border-border">
            <div aria-hidden="true" class="pointer-events-none absolute -top-40 left-1/2 -z-10 h-80 w-[40rem] -translate-x-1/2 rounded-full bg-accent/15 blur-3xl"></div>

            <div class="container-page py-10 sm:py-14">
                <a href="{{ route('home') }}#projects" class="link inline-flex items-center gap-1 text-sm">
                    <x-heroicon-m-arrow-left class="size-4" aria-hidden="true" />
                    {{ __('site.project.back') }}
                </a>

                <div class="animate-enter">
                    <div class="mt-8 flex flex-wrap items-center gap-2">
                        <x-site.project-status :status="$project->status" />
                        @if ($project->year)
                            <span class="font-mono text-sm text-muted">{{ $project->year }}</span>
                        @endif
                        @if (filled($project->company))
                            <span class="text-muted" aria-hidden="true">·</span>
                            <span class="text-sm text-muted">{{ $project->company }}</span>
                        @endif
                    </div>

                    <h1 class="mt-4 text-4xl font-bold tracking-tight text-balance sm:text-5xl">{{ $project->title }}</h1>
                    @if (filled($project->subtitle))
                        <p class="mt-3 font-mono text-sm text-accent">{{ $project->subtitle }}</p>
                    @endif
                    @if (filled($project->tagline))
                        <p class="mt-5 max-w-3xl text-xl leading-relaxed text-muted text-pretty">{{ $project->tagline }}</p>
                    @endif
                </div>

                @if (filled($project->cover_image))
                    <x-responsive-image
                        :path="$project->cover_image"
                        :alt="$project->cover_image_alt ?? ''"
                        sizes="(min-width: 1152px) 1088px, calc(100vw - 2rem)"
                        :priority="true"
                        class="animate-enter mt-10 aspect-video w-full rounded-2xl border border-border object-cover object-top shadow-2xl shadow-accent/10"
                    />
                @endif
            </div>
        </header>

        <div class="container-page grid gap-12 py-12 sm:py-16 lg:grid-cols-[minmax(0,1fr)_20rem] lg:gap-16">
            <div class="min-w-0 space-y-14">
                @if (filled($project->summary))
                    <p class="reveal text-lg leading-relaxed text-pretty">{{ $project->summary }}</p>
                @endif

                @foreach ($chapters as $chapter => $html)
                    <section aria-labelledby="{{ $chapter }}-title" class="reveal">
                        <p class="eyebrow">{{ sprintf('%02d', $loop->iteration) }}</p>
                        <h2 id="{{ $chapter }}-title" class="mt-1 text-2xl font-bold tracking-tight">{{ __("site.project.{$chapter}") }}</h2>
                        @if ($chapter === 'integrations')
                            <ul class="mt-5 flex flex-wrap gap-2">
                                @foreach ($project->integrations as $integration)
                                    <li class="badge badge-accent px-3 py-1 text-sm">{{ $integration }}</li>
                                @endforeach
                            </ul>
                        @else
                            <div class="prose-content mt-5 text-lg">{!! in_array($chapter, ['problem', 'my_role'], true) ? '<p>'.$html.'</p>' : $html !!}</div>
                        @endif
                    </section>
                @endforeach

                @if ($gallery->isNotEmpty())
                    <section aria-labelledby="gallery-title" class="reveal" x-data="lightbox(@js($gallery))">
                        <h2 id="gallery-title" class="text-2xl font-bold tracking-tight">{{ __('site.project.gallery') }}</h2>
                        <ul class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3">
                            @foreach ($gallery as $index => $image)
                                <li>
                                    <button
                                        type="button"
                                        x-on:click="show({{ $index }})"
                                        class="block w-full overflow-hidden rounded-xl border border-border transition-colors hover:border-accent"
                                    >
                                        <span class="sr-only">{{ __('site.project.open_image', ['alt' => $image['alt']]) }}</span>
                                        <x-responsive-image :path="$image['path']" sizes="(min-width: 640px) 33vw, 50vw" class="aspect-video w-full object-cover" />
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        <template x-teleport="body">
                            <div
                                x-cloak
                                x-show="isOpen"
                                x-transition.opacity
                                x-trap.inert.noscroll="isOpen"
                                x-on:keydown.escape.prevent.stop="close()"
                                x-on:keydown.arrow-right="next()"
                                x-on:keydown.arrow-left="previous()"
                                class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4 sm:p-10"
                                role="dialog"
                                aria-modal="true"
                                aria-label="{{ __('site.lightbox.label') }}"
                            >
                                <button type="button" x-on:click="close()" class="absolute top-4 right-4 inline-flex size-11 items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20">
                                    <span class="sr-only">{{ __('site.lightbox.close') }}</span>
                                    <x-heroicon-o-x-mark class="size-6" aria-hidden="true" />
                                </button>

                                @if ($gallery->count() > 1)
                                    <button type="button" x-on:click="previous()" class="absolute left-2 inline-flex size-11 items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 sm:left-6">
                                        <span class="sr-only">{{ __('site.lightbox.previous') }}</span>
                                        <x-heroicon-o-chevron-left class="size-6" aria-hidden="true" />
                                    </button>
                                    <button type="button" x-on:click="next()" class="absolute right-2 inline-flex size-11 items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 sm:right-6">
                                        <span class="sr-only">{{ __('site.lightbox.next') }}</span>
                                        <x-heroicon-o-chevron-right class="size-6" aria-hidden="true" />
                                    </button>
                                @endif

                                <figure class="flex max-h-full max-w-5xl flex-col items-center">
                                    <img :src="current?.src" :alt="current?.alt" class="max-h-[78vh] w-auto rounded-lg">
                                    <figcaption class="mt-4 text-center text-sm text-white/80">
                                        <span x-text="current?.caption ?? current?.alt"></span>
                                        <span class="mt-1 block font-mono text-xs text-white/60" x-text="@js(__('site.lightbox.counter')).replace(':current', index + 1).replace(':total', images.length)"></span>
                                    </figcaption>
                                </figure>
                            </div>
                        </template>
                    </section>
                @endif
            </div>

            <aside aria-labelledby="facts-title" class="h-fit lg:sticky lg:top-24">
                <div class="card p-6">
                    <h2 id="facts-title" class="text-sm font-semibold">{{ __('site.project.facts') }}</h2>
                    <dl class="mt-5 space-y-5 text-sm">
                        @if (filled($project->company))
                            <div>
                                <dt class="text-muted">{{ __('site.project.company') }}</dt>
                                <dd class="mt-1 font-medium">{{ $project->company }}</dd>
                            </div>
                        @endif
                        @if ($project->year)
                            <div>
                                <dt class="text-muted">{{ __('site.project.year') }}</dt>
                                <dd class="mt-1 font-mono">{{ $project->year }}</dd>
                            </div>
                        @endif
                        @if ($project->status)
                            <div>
                                <dt class="text-muted">{{ __('site.project.status') }}</dt>
                                <dd class="mt-1"><x-site.project-status :status="$project->status" /></dd>
                            </div>
                        @endif
                        @if (filled($project->tech_stack))
                            <div>
                                <dt class="text-muted">{{ __('site.project.stack') }}</dt>
                                <dd class="mt-2 flex flex-wrap gap-1.5">
                                    @foreach ($project->tech_stack as $tech)
                                        <span class="chip">{{ $tech }}</span>
                                    @endforeach
                                </dd>
                            </div>
                        @endif
                        @if (filled($project->integrations))
                            <div>
                                <dt class="text-muted">{{ __('site.project.integrations') }}</dt>
                                <dd class="mt-2 flex flex-wrap gap-1.5">
                                    @foreach ($project->integrations as $integration)
                                        <span class="badge badge-accent">{{ $integration }}</span>
                                    @endforeach
                                </dd>
                            </div>
                        @endif
                    </dl>

                    @if (filled($project->links))
                        <h3 class="mt-6 border-t border-border pt-5 text-sm font-semibold">{{ __('site.project.links') }}</h3>
                        <ul class="mt-3 space-y-2">
                            @foreach ($project->links as $link)
                                <li>
                                    <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="group flex items-center gap-2 text-sm">
                                        <x-site.link-icon :type="$link['type'] ?? 'other'" class="size-4 text-muted group-hover:text-accent" />
                                        <span class="link">{{ $link['label'] }}</span>
                                        <span class="sr-only">{{ __('site.project.opens_in_new_tab') }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </aside>
        </div>

        <footer class="border-t border-border bg-surface/40">
            <div class="container-page grid gap-6 py-12 md:grid-cols-2">
                @if ($page->nextProject)
                    <a href="{{ route('projects.show', $page->nextProject->slug) }}" class="card group p-6 transition-colors hover:border-accent/60">
                        <p class="eyebrow">{{ __('site.project.next') }} →</p>
                        <p class="mt-2 text-xl font-semibold group-hover:text-accent">{{ $page->nextProject->title }}</p>
                        @if (filled($page->nextProject->subtitle))
                            <p class="mt-1 font-mono text-xs text-muted">{{ $page->nextProject->subtitle }}</p>
                        @endif
                    </a>
                @endif
                <div class="card flex flex-col justify-center p-6">
                    <p class="text-xl font-semibold">{{ __('site.project.cta_title') }}</p>
                    <p class="mt-2 text-muted">{{ __('site.project.cta_text') }}</p>
                    <div class="mt-5">
                        <a href="{{ route('home', ['inquiry' => \App\Enums\ContactInquiryType::FreelanceProject->value]) }}#contact" class="btn btn-primary">{{ __('site.project.cta_button') }}</a>
                    </div>
                </div>
            </div>
        </footer>
    </article>
</x-layouts.public>
