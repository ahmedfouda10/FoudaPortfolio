@props(['settings'])
@php
    $stats = array_filter([
        'years_of_experience' => $settings->years_of_experience !== null ? $settings->years_of_experience.'+' : null,
        'projects_shipped' => $settings->projects_shipped,
        'platforms_integrated' => $settings->platforms_integrated,
    ], fn (int|string|null $value): bool => $value !== null);
@endphp
<section id="top" aria-labelledby="hero-title" class="relative isolate overflow-hidden">
    {{-- Decorative grid + glow --}}
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[linear-gradient(to_right,var(--border)_1px,transparent_1px),linear-gradient(to_bottom,var(--border)_1px,transparent_1px)] bg-[size:3.5rem_3.5rem] [mask-image:radial-gradient(ellipse_at_top,black_20%,transparent_70%)] opacity-50"></div>
    <div aria-hidden="true" class="pointer-events-none absolute -top-40 left-1/2 -z-10 h-80 w-[40rem] -translate-x-1/2 rounded-full bg-accent/20 blur-3xl"></div>

    <div class="container-page grid items-center gap-12 py-16 sm:py-24 lg:grid-cols-[minmax(0,1fr)_auto] lg:py-28">
        <div class="animate-enter">
            @if (filled($settings->availability_status))
                <p class="badge badge-success">
                    <span class="relative flex size-2" aria-hidden="true">
                        <span class="absolute inline-flex size-full rounded-full bg-emerald-400 opacity-75 motion-safe:animate-ping"></span>
                        <span class="relative inline-flex size-2 rounded-full bg-emerald-500"></span>
                    </span>
                    {{ $settings->availability_status }}
                </p>
            @endif

            <h1 id="hero-title" class="mt-6 text-4xl font-bold tracking-tight text-balance sm:text-6xl">{{ $settings->full_name }}</h1>

            @if (filled($settings->headline))
                <p class="mt-4 text-lg font-medium text-accent text-balance sm:text-xl">{{ $settings->headline }}</p>
            @endif

            @if (filled($settings->short_bio))
                <p class="mt-6 max-w-2xl text-lg leading-relaxed text-muted text-pretty">{{ $settings->short_bio }}</p>
            @endif

            <div class="mt-8 flex flex-wrap gap-3">
                @if (filled($settings->cv_file))
                    <a href="{{ route('cv.download') }}" class="btn btn-primary" rel="nofollow">
                        <x-heroicon-o-arrow-down-tray class="size-4" aria-hidden="true" />
                        {{ __('site.hero.download_cv') }}
                    </a>
                @endif
                <a href="#contact" class="btn btn-secondary">
                    <x-heroicon-o-chat-bubble-left-right class="size-4" aria-hidden="true" />
                    {{ __('site.hero.contact_me') }}
                </a>
            </div>

            @if ($stats !== [])
                <dl class="mt-10 grid max-w-xl grid-cols-3 gap-4 border-t border-border pt-8" aria-label="{{ __('site.hero.stats') }}">
                    @foreach ($stats as $label => $value)
                        <div class="flex flex-col gap-1">
                            <dt class="text-xs text-muted sm:text-sm">{{ __("site.hero.{$label}") }}</dt>
                            <dd class="order-first font-mono text-2xl font-semibold sm:text-3xl">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            @endif

            <x-site.social-links :settings="$settings" class="-ms-2 mt-8" />
        </div>

        @if (filled($settings->avatar))
            <div class="animate-enter mx-auto lg:mx-0">
                <x-responsive-image
                    :path="$settings->avatar"
                    :alt="__('site.hero.photo_alt', ['name' => $settings->full_name])"
                    sizes="(min-width: 1024px) 320px, (min-width: 640px) 288px, 224px"
                    :priority="true"
                    :fallback-width="320"
                    :fallback-height="320"
                    class="size-56 rounded-3xl border border-border object-cover shadow-2xl shadow-accent/10 sm:size-72 lg:size-80"
                />
            </div>
        @endif
    </div>
</section>
