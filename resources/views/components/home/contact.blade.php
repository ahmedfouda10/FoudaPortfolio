@props(['settings'])
@php
    $whatsapp = filled($settings->whatsapp) ? preg_replace('/\D+/', '', $settings->whatsapp) : null;
    $channels = array_filter([
        ['label' => __('site.contact.email'), 'value' => $settings->email, 'url' => $settings->email ? 'mailto:'.$settings->email : null, 'icon' => 'heroicon-o-envelope', 'external' => false],
        ['label' => __('site.contact.linkedin'), 'value' => 'linkedin.com', 'url' => $settings->linkedin_url, 'icon' => 'icons.linkedin', 'external' => true],
        ['label' => __('site.contact.whatsapp'), 'value' => $settings->whatsapp, 'url' => $whatsapp ? "https://wa.me/{$whatsapp}" : null, 'icon' => 'heroicon-o-chat-bubble-left-right', 'external' => true],
    ], fn (array $channel): bool => filled($channel['url']));
@endphp
<x-site.section id="contact" :eyebrow="__('site.sections.contact.eyebrow')" :title="__('site.sections.contact.title')">
    <div class="grid gap-10 lg:grid-cols-[2fr_3fr] lg:gap-16">
        <div class="reveal">
            <p class="text-lg leading-relaxed text-muted">{{ __('site.contact.intro') }}</p>
            @if (filled($settings->availability_status))
                <p class="badge badge-success mt-6">{{ $settings->availability_status }}</p>
            @endif

            @if ($channels !== [])
                <h3 class="mt-10 text-sm font-semibold">{{ __('site.contact.direct') }}</h3>
                <ul class="mt-4 space-y-3">
                    @foreach ($channels as $channel)
                        <li>
                            <a
                                href="{{ $channel['url'] }}"
                                @if ($channel['external']) target="_blank" rel="noopener noreferrer" @endif
                                class="group flex items-center gap-3"
                            >
                                <span class="inline-flex size-10 items-center justify-center rounded-lg border border-border bg-surface text-muted transition-colors group-hover:border-accent group-hover:text-accent">
                                    <x-dynamic-component :component="$channel['icon']" class="size-5" aria-hidden="true" />
                                </span>
                                <span>
                                    <span class="block text-xs text-muted">{{ $channel['label'] }}</span>
                                    <span class="block font-medium group-hover:text-accent">{{ $channel['value'] }}</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="reveal">
            {{ $slot }}
        </div>
    </div>
</x-site.section>
