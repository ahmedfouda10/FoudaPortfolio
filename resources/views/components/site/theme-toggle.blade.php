<button
    type="button"
    x-data="theme"
    x-on:click="toggle()"
    class="inline-flex size-10 items-center justify-center rounded-lg text-muted transition-colors hover:text-foreground"
    aria-label="{{ __('site.theme.switch_to_light') }}"
    :aria-label="dark ? @js(__('site.theme.switch_to_light')) : @js(__('site.theme.switch_to_dark'))"
>
    <x-heroicon-o-sun class="size-5" x-show="dark" aria-hidden="true" />
    <x-heroicon-o-moon class="size-5" x-show="! dark" x-cloak aria-hidden="true" />
</button>
