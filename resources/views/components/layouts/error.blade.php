@props(['code', 'title', 'message'])
{{-- Standalone on purpose: no database access, so it still renders when the database is down. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $code }} — {{ $title }}</title>
    <script>
        (function () {
            var stored = null;
            try { stored = window.localStorage.getItem('theme'); } catch (e) {}
            var prefersLight = window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches;
            document.documentElement.classList.toggle('dark', stored ? stored === 'dark' : !prefersLight);
        })();
    </script>
    @fonts
    @vite('resources/css/app.css')
</head>
<body class="flex min-h-screen items-center justify-center">
    <main class="container-page py-16 text-center">
        <p class="font-mono text-6xl font-bold text-accent sm:text-7xl">{{ $code }}</p>
        <h1 class="mt-6 text-2xl font-bold tracking-tight sm:text-3xl">{{ $title }}</h1>
        <p class="mx-auto mt-3 max-w-md text-muted">{{ $message }}</p>
        <a href="{{ url('/') }}" class="btn btn-primary mt-8">{{ __('site.errors.back_home') }}</a>
    </main>
</body>
</html>
