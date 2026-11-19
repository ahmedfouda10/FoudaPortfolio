@php
    /** @var \App\Data\HomePageContent $content */
    $settings = $content->settings;
    $sections = $content->sections();
@endphp
<x-layouts.public
    :settings="$settings"
    :title="$settings->meta_title ?: trim($settings->full_name.' — '.$settings->headline, ' —')"
    :description="$settings->meta_description ?: $settings->short_bio"
    :on-home="true"
    :sections="$sections"
>
    <x-home.hero :settings="$settings" />

    @if (in_array('about', $sections, true))
        <x-home.about :settings="$settings" />
    @endif

    @if (in_array('experience', $sections, true))
        <x-home.experience :experiences="$content->experiences" />
    @endif

    @if (in_array('skills', $sections, true))
        <x-home.skills :skill-groups="$content->skillGroups" />
    @endif

    @if (in_array('services', $sections, true))
        <x-home.services :services="$content->services" />
    @endif

    @if (in_array('projects', $sections, true))
        <x-home.projects :projects="$content->projects" :integrations="$content->integrations()" />
    @endif

    @if (in_array('education', $sections, true))
        <x-home.education :education="$content->education" :certifications="$content->certifications" />
    @endif

    <x-home.contact :settings="$settings">
        <livewire:contact-form />
    </x-home.contact>
</x-layouts.public>
