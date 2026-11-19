@props(['experiences'])
<x-site.section id="experience" :eyebrow="__('site.sections.experience.eyebrow')" :title="__('site.sections.experience.title')" class="bg-surface/40">
    <ol class="relative ms-2 space-y-12 border-s border-border">
        @foreach ($experiences as $experience)
            <li class="reveal relative ps-8 sm:ps-10">
                <span aria-hidden="true" class="absolute top-1.5 -start-[0.5625rem] size-4 rounded-full border-2 border-accent bg-background"></span>

                <div class="flex flex-wrap items-center justify-between gap-x-6 gap-y-2">
                    <h3 class="text-xl font-semibold">
                        {{ $experience->role }}
                        <span class="text-muted" aria-hidden="true">·</span>
                        <span class="text-accent">{{ $experience->company }}</span>
                    </h3>
                    <p class="flex items-center gap-2 font-mono text-sm text-muted">
                        <time datetime="{{ $experience->start_date->format('Y-m') }}">{{ $experience->start_date->translatedFormat('M Y') }}</time>
                        <span aria-hidden="true">–</span>
                        @if ($experience->isCurrent())
                            <span class="badge badge-accent">{{ __('site.experience.present') }}</span>
                        @else
                            <time datetime="{{ $experience->end_date?->format('Y-m') }}">{{ $experience->end_date?->translatedFormat('M Y') }}</time>
                        @endif
                    </p>
                </div>

                @if (filled($experience->location) || $experience->employment_type !== null)
                    <p class="mt-1 text-sm text-muted">
                        {{ collect([$experience->location, $experience->employment_type?->getLabel()])->filter()->implode(' · ') }}
                    </p>
                @endif

                @if (filled($experience->description))
                    <p class="mt-4 leading-relaxed text-muted">{{ $experience->description }}</p>
                @endif

                @if (filled($experience->bullet_points))
                    <ul class="mt-4 space-y-2.5 leading-relaxed text-muted">
                        @foreach ($experience->bullet_points as $point)
                            <li class="flex gap-3">
                                <span aria-hidden="true" class="mt-2.5 size-1.5 shrink-0 rounded-full bg-accent"></span>
                                <span>{{ $point }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ol>
</x-site.section>
