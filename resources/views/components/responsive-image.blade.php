<img
    src="{{ $src }}"
    @if ($srcset) srcset="{{ $srcset }}" sizes="{{ $sizes }}" @endif
    alt="{{ $alt }}"
    width="{{ $width }}"
    height="{{ $height }}"
    @if ($priority) fetchpriority="high" @else loading="lazy" @endif
    decoding="async"
    {{ $attributes }}
>
