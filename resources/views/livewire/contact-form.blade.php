<div
    class="card p-6 sm:p-8"
    data-focus-first-error
    x-data
    x-on:contact-inquiry.window="
        $wire.set('form.inquiryType', $event.detail.type);
        if ($event.detail.subject && ! $wire.form.subject) { $wire.form.subject = $event.detail.subject }
    "
>
    @if ($sent)
        <div role="status" class="py-10 text-center" x-init="$nextTick(() => $refs.successTitle.focus())">
            <span class="mx-auto inline-flex size-14 items-center justify-center rounded-full bg-emerald-500/12 text-emerald-600 dark:text-emerald-300" aria-hidden="true">
                <x-heroicon-o-check class="size-7" />
            </span>
            <h3 x-ref="successTitle" tabindex="-1" class="mt-5 text-xl font-semibold focus:outline-none">{{ __('contact.success.title') }}</h3>
            <p class="mt-2 text-muted">{{ __('contact.success.body') }}</p>
            <button type="button" wire:click="startOver" class="btn btn-secondary mt-6">{{ __('contact.success.again') }}</button>
        </div>
    @else
        <form wire:submit="submit" novalidate class="space-y-5">
            <h3 class="text-lg font-semibold">{{ __('contact.title') }}</h3>

            @error('form')
                <div role="alert" class="rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-700 dark:text-red-300">{{ $message }}</div>
            @enderror

            <div class="grid gap-5 sm:grid-cols-2">
                <x-form.input name="form.name" :label="__('contact.fields.name')" autocomplete="name" :placeholder="__('contact.placeholders.name')" maxlength="100" />
                <x-form.input name="form.email" type="email" :label="__('contact.fields.email')" autocomplete="email" inputmode="email" :placeholder="__('contact.placeholders.email')" maxlength="255" />
            </div>

            <fieldset>
                <legend class="text-sm font-medium">{{ __('contact.fields.inquiry_type') }}<span class="text-accent" aria-hidden="true"> *</span></legend>
                <div class="mt-2 grid gap-2 sm:grid-cols-3">
                    @foreach ($inquiryTypes as $type)
                        <label class="flex cursor-pointer items-center gap-2.5 rounded-lg border border-border bg-background px-3.5 py-2.5 text-sm transition-colors hover:border-accent has-[:checked]:border-accent has-[:checked]:bg-accent/10 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-accent/40">
                            <input type="radio" wire:model.live="form.inquiryType" value="{{ $type->value }}" class="size-4 accent-[var(--accent)]">
                            {{ $type->getLabel() }}
                        </label>
                    @endforeach
                </div>
                @error('form.inquiryType')
                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </fieldset>

            @if ($form->acceptsBudget())
                <x-form.input name="form.budget" :label="__('contact.fields.budget')" :hint="__('contact.hints.budget')" :placeholder="__('contact.placeholders.budget')" maxlength="100" />
            @endif

            <x-form.input name="form.subject" :label="__('contact.fields.subject')" :placeholder="__('contact.placeholders.subject')" maxlength="150" />
            <x-form.input name="form.message" :label="__('contact.fields.message')" :placeholder="__('contact.placeholders.message')" :multiline="true" maxlength="5000" />

            {{-- Honeypot: hidden from people; bots that fill it in get a fake success. --}}
            <div class="absolute -left-[9999px] h-px w-px overflow-hidden" aria-hidden="true">
                <label for="contact-website">{{ __('contact.fields.website') }}</label>
                <input id="contact-website" type="text" wire:model="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4 pt-1">
                <p class="text-xs text-muted">{{ __('contact.privacy') }}</p>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="submit">
                    <span wire:loading.remove wire:target="submit" class="inline-flex items-center gap-2">
                        <x-heroicon-o-paper-airplane class="size-4" aria-hidden="true" />
                        {{ __('contact.submit') }}
                    </span>
                    <span wire:loading wire:target="submit">{{ __('contact.sending') }}</span>
                </button>
            </div>
        </form>
    @endif
</div>
