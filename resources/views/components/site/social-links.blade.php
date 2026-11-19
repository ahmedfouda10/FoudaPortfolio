@props(['settings'])
@php
    $links = array_filter([
        ['label' => __('site.contact.linkedin'), 'url' => $settings->linkedin_url, 'icon' => 'icons.linkedin'],
        ['label' => __('site.contact.github'), 'url' => $settings->github_url, 'icon' => 'icons.github'],
        ['label' => __('site.contact.freelancer'), 'url' => $settings->freelancer_url, 'icon' => 'heroicon-o-briefcase'],
        ['label' => __('site.contact.email'), 'url' => $settings->email ? 'mailto:'.$settings->email : null, 'icon' => 'heroicon-o-envelope'],
    ], fn (array $link): bool => filled($link['url']));
@endphp
@if ($links !== [])
    <ul {{ $attributes->merge(['class' => 'flex items-center gap-1']) }}>
        @foreach ($links as $link)
            <li>
                <a
                    href="{{ $link['url'] }}"
                    @unless (str_starts_with($link['url'], 'mailto:')) target="_blank" rel="noopener noreferrer me" @endunless
                    class="inline-flex size-10 items-center justify-center rounded-lg text-muted transition-colors hover:bg-surface-muted hover:text-accent"
                >
                    <span class="sr-only">{{ $link['label'] }}</span>
                    <x-dynamic-component :component="$link['icon']" class="size-5" aria-hidden="true" />
                </a>
            </li>
        @endforeach
    </ul>
@endif
