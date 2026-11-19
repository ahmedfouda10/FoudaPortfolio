@props(['id', 'eyebrow', 'title'])
<section id="{{ $id }}" aria-labelledby="{{ $id }}-title" {{ $attributes->merge(['class' => 'scroll-mt-16 py-20 sm:py-28']) }}>
    <div class="container-page">
        <header class="reveal mb-10 sm:mb-14">
            <p class="eyebrow">{{ $eyebrow }}</p>
            <h2 id="{{ $id }}-title" class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">{{ $title }}</h2>
        </header>

        {{ $slot }}
    </div>
</section>
