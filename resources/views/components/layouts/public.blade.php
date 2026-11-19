@props([
    'settings',
    'seo',
    'onHome' => false,
    'sections' => [],
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <x-site.seo :seo="$seo" />
    <meta name="color-scheme" content="dark light">
    <meta name="theme-color" content="#18181f" media="(prefers-color-scheme: dark)">
    <meta name="theme-color" content="#fbfbfd" media="(prefers-color-scheme: light)">
    <link rel="icon" href="{{ route('favicon') }}" type="image/svg+xml">

    {{-- Applies the saved (or system) theme before first paint: no flash of the wrong theme. Dark by default. --}}
    <script>
        (function () {
            var stored = null;
            try { stored = window.localStorage.getItem('theme'); } catch (e) {}
            var prefersLight = window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches;
            document.documentElement.classList.toggle('dark', stored ? stored === 'dark' : !prefersLight);
        })();
    </script>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {{ $head ?? '' }}
</head>
<body class="min-h-screen">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:rounded-lg focus:bg-accent focus:px-4 focus:py-2 focus:text-accent-contrast">
        {{ __('site.skip_to_content') }}
    </a>

    <x-site.navbar :settings="$settings" :on-home="$onHome" :sections="$sections" />

    <main id="main" tabindex="-1">
        {{ $slot }}
    </main>

    <x-site.footer :settings="$settings" :sections="$sections" />

    @livewireScriptConfig
</body>
</html>
