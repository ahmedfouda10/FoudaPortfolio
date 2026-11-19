@props(['services'])
@php($icons = \App\Filament\Support\HeroiconOptions::outline())
<x-site.section id="services" :eyebrow="__('site.sections.services.eyebrow')" :title="__('site.sections.services.title')" class="bg-surface/40">
    <ul class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($services as $service)
            <li class="card reveal flex flex-col p-6 transition-colors hover:border-accent/60">
                <span class="inline-flex size-11 items-center justify-center rounded-xl bg-accent/12 text-accent" aria-hidden="true">
                    <x-dynamic-component :component="array_key_exists((string) $service->icon, $icons) ? $service->icon : 'heroicon-o-sparkles'" class="size-6" />
                </span>
                <h3 class="mt-5 text-lg font-semibold">{{ $service->title }}</h3>
                <p class="mt-2 flex-1 leading-relaxed text-muted">{{ $service->short_description }}</p>
                <a
                    href="#contact"
                    class="link mt-4 inline-flex min-h-10 items-center gap-1 self-start text-sm"
                    x-data
                    x-on:click="$dispatch('contact-inquiry', { type: 'freelance_project', subject: @js($service->title) })"
                >
                    {{ __('site.services.discuss') }}
                    <x-heroicon-m-arrow-right class="size-4" aria-hidden="true" />
                    <span class="sr-only">: {{ $service->title }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</x-site.section>
