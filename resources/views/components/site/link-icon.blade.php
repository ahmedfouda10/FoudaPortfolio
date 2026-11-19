@props(['type'])
@switch($type)
    @case('github')
        <x-icons.github {{ $attributes }} />
        @break
    @case('shopify_app')
        <x-heroicon-o-shopping-bag {{ $attributes }} aria-hidden="true" />
        @break
    @case('salla_app')
        <x-heroicon-o-building-storefront {{ $attributes }} aria-hidden="true" />
        @break
    @case('website')
        <x-heroicon-o-globe-alt {{ $attributes }} aria-hidden="true" />
        @break
    @default
        <x-heroicon-o-arrow-top-right-on-square {{ $attributes }} aria-hidden="true" />
@endswitch
