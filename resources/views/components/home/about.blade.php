@props(['settings'])
<x-site.section id="about" :eyebrow="__('site.sections.about.eyebrow')" :title="__('site.sections.about.title')">
    <div class="grid gap-10 lg:grid-cols-[3fr_2fr] lg:gap-16">
        @if (filled($settings->about_body))
            <div class="prose-content reveal text-lg">
                {!! Str::sanitizeHtml($settings->about_body) !!}
            </div>
        @endif

        @if (filled($settings->about_highlights))
            <ul class="card reveal h-fit space-y-4 p-6">
                @foreach ($settings->about_highlights as $highlight)
                    <li class="flex gap-3">
                        <x-heroicon-o-check-circle class="mt-0.5 size-5 shrink-0 text-accent" aria-hidden="true" />
                        <span>{{ $highlight }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-site.section>
