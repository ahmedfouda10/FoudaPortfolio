@props(['settings', 'onHome' => false, 'sections' => []])
@php
    $links = array_values(array_filter($sections, fn (string $id): bool => $id !== 'contact'));
    $href = fn (string $id): string => $onHome ? "#{$id}" : route('home')."#{$id}";
@endphp
<header
    x-data="navigation(@js($onHome ? $sections : []))"
    class="sticky top-0 z-40 border-b border-border/70 bg-background/85 backdrop-blur-md"
>
    <nav aria-label="{{ __('site.nav.label') }}" class="container-page flex h-16 items-center justify-between gap-4">
        <a href="{{ $onHome ? '#top' : route('home') }}" class="inline-flex min-h-10 shrink-0 items-center font-mono text-sm font-semibold tracking-tight">
            <span class="text-accent" aria-hidden="true">&lt;</span>{{ $settings->full_name }}<span class="text-accent" aria-hidden="true"> /&gt;</span>
        </a>

        <ul class="hidden items-center gap-1 lg:flex">
            @foreach ($links as $id)
                <li>
                    <a
                        href="{{ $href($id) }}"
                        class="rounded-md px-3 py-2 text-sm text-muted transition-colors hover:text-foreground aria-[current=true]:text-foreground aria-[current=true]:font-medium"
                        :aria-current="isActive(@js($id)) ? 'true' : null"
                    >{{ __("site.nav.{$id}") }}</a>
                </li>
            @endforeach
        </ul>

        <div class="flex items-center gap-2">
            <x-site.theme-toggle />

            @if (in_array('contact', $sections, true))
                <a
                    href="{{ $href('contact') }}"
                    class="btn btn-primary hidden sm:inline-flex"
                    x-on:click="$dispatch('contact-inquiry', { type: 'freelance_project' })"
                >{{ __('site.nav.hire_me') }}</a>
            @endif

            <button
                type="button"
                class="inline-flex size-10 items-center justify-center rounded-lg text-muted hover:text-foreground lg:hidden"
                x-on:click="toggle()"
                aria-controls="mobile-menu"
                aria-expanded="false"
                :aria-expanded="open.toString()"
            >
                <span class="sr-only">{{ __('site.nav.open_menu') }}</span>
                <x-heroicon-o-bars-3 class="size-6" aria-hidden="true" />
            </button>
        </div>
    </nav>

    {{-- Mobile menu: full-screen panel with its own close button, focus trap and scroll lock. --}}
    <div
        id="mobile-menu"
        x-cloak
        x-show="open"
        x-transition.opacity
        x-trap.inert.noscroll="open"
        x-on:keydown.escape.window="close()"
        class="fixed inset-0 z-50 bg-background lg:hidden"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('site.nav.label') }}"
    >
        <div class="container-page flex h-16 items-center justify-between">
            <span class="font-mono text-sm font-semibold">{{ $settings->full_name }}</span>
            <button type="button" class="inline-flex size-10 items-center justify-center rounded-lg text-muted hover:text-foreground" x-on:click="close()">
                <span class="sr-only">{{ __('site.nav.close_menu') }}</span>
                <x-heroicon-o-x-mark class="size-6" aria-hidden="true" />
            </button>
        </div>
        <ul class="container-page mt-4 space-y-1">
            @foreach ($links as $id)
                <li>
                    <a href="{{ $href($id) }}" x-on:click="close()" class="block rounded-lg px-3 py-3 text-lg font-medium hover:bg-surface-muted">
                        {{ __("site.nav.{$id}") }}
                    </a>
                </li>
            @endforeach
            @if (in_array('contact', $sections, true))
                <li class="pt-4">
                    <a
                        href="{{ $href('contact') }}"
                        class="btn btn-primary w-full"
                        x-on:click="close(); $dispatch('contact-inquiry', { type: 'freelance_project' })"
                    >{{ __('site.nav.hire_me') }}</a>
                </li>
            @endif
        </ul>
    </div>
</header>
