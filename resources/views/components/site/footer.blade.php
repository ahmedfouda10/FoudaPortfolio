@props(['settings', 'sections' => []])
<footer class="border-t border-border bg-surface/40">
    <div class="container-page grid gap-10 py-12 sm:grid-cols-2 lg:grid-cols-3">
        <div>
            <p class="font-mono text-sm font-semibold">
                <span class="text-accent" aria-hidden="true">&lt;</span>{{ $settings->full_name }}<span class="text-accent" aria-hidden="true"> /&gt;</span>
            </p>
            @if (filled($settings->headline))
                <p class="mt-3 max-w-xs text-sm leading-relaxed text-muted">{{ $settings->headline }}</p>
            @endif
            <x-site.social-links :settings="$settings" class="-ms-2 mt-4" />
        </div>

        @if ($sections !== [])
            <nav aria-labelledby="footer-links-title">
                <h2 id="footer-links-title" class="text-sm font-semibold">{{ __('site.footer.quick_links') }}</h2>
                <ul class="mt-4 grid grid-cols-2 gap-2 text-sm">
                    @foreach ($sections as $id)
                        <li><a href="{{ route('home') }}#{{ $id }}" class="text-muted hover:text-accent">{{ __("site.nav.{$id}") }}</a></li>
                    @endforeach
                </ul>
            </nav>
        @endif

        @if (filled($settings->source_code_url))
            <div>
                <a href="{{ $settings->source_code_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-sm text-muted hover:text-accent">
                    <x-icons.github class="size-5" />
                    {{ __('site.footer.open_source') }}
                </a>
            </div>
        @endif
    </div>

    <div class="border-t border-border">
        <p class="container-page py-6 text-sm text-muted">
            {{ __('site.footer.copyright', ['year' => now()->year, 'name' => $settings->full_name]) }}
        </p>
    </div>
</footer>
