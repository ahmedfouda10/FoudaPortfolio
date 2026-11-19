@props(['name', 'label', 'type' => 'text', 'required' => true, 'hint' => null, 'multiline' => false])
@php
    $id = 'contact-'.str_replace('.', '-', $name);
    $fieldName = str($name)->afterLast('.')->snake()->toString();
    $hasError = $errors->has($name);
    $describedBy = collect([$hint ? "{$id}-hint" : null, $hasError ? "{$id}-error" : null])->filter()->implode(' ');
    $classes = 'mt-1.5 block w-full rounded-lg border bg-background px-3.5 py-2.5 text-foreground placeholder:text-muted/70 transition-colors focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 '
        .($hasError ? 'border-red-500/70' : 'border-border');
@endphp
<div>
    <label for="{{ $id }}" class="block text-sm font-medium">
        {{ $label }}@if ($required)<span class="text-accent" aria-hidden="true"> *</span>@endif
    </label>

    @if ($multiline)
        <textarea
            id="{{ $id }}"
            name="{{ $fieldName }}"
            wire:model="{{ $name }}"
            @if ($required) required @endif
            aria-invalid="{{ $hasError ? 'true' : 'false' }}"
            @if ($describedBy !== '') aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->merge(['class' => $classes, 'rows' => 6]) }}
        ></textarea>
    @else
        <input
            id="{{ $id }}"
            name="{{ $fieldName }}"
            type="{{ $type }}"
            wire:model="{{ $name }}"
            @if ($required) required @endif
            aria-invalid="{{ $hasError ? 'true' : 'false' }}"
            @if ($describedBy !== '') aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->merge(['class' => $classes]) }}
        >
    @endif

    @if ($hint)
        <p id="{{ $id }}-hint" class="mt-1.5 text-xs text-muted">{{ $hint }}</p>
    @endif

    @error($name)
        <p id="{{ $id }}-error" class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
