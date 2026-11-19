@props(['projects', 'integrations'])
<x-site.section id="projects" :eyebrow="__('site.sections.projects.eyebrow')" :title="__('site.sections.projects.title')">
    <div x-data="projectFilter(@js($projects->map(fn ($project) => $project->integrations ?? [])->values()))">
        @if (count($integrations) > 1)
            <div x-cloak role="group" aria-label="{{ __('site.projects.filter_label') }}" class="mb-8 flex flex-wrap gap-2">
                <button
                    type="button"
                    x-on:click="select(null)"
                    :aria-pressed="isSelected(null).toString()"
                    class="rounded-full border border-border px-3 py-1.5 font-mono text-xs text-muted transition-colors hover:border-accent hover:text-accent aria-pressed:border-accent aria-pressed:bg-accent aria-pressed:text-accent-contrast"
                >{{ __('site.projects.all') }}</button>
                @foreach ($integrations as $integration)
                    <button
                        type="button"
                        x-on:click="select(@js($integration))"
                        :aria-pressed="isSelected(@js($integration)).toString()"
                        class="rounded-full border border-border px-3 py-1.5 font-mono text-xs text-muted transition-colors hover:border-accent hover:text-accent aria-pressed:border-accent aria-pressed:bg-accent aria-pressed:text-accent-contrast"
                    >{{ $integration }}</button>
                @endforeach
            </div>
            <p x-cloak class="sr-only" aria-live="polite" x-text="@js(__('site.projects.showing')).replace(':count', visibleCount).replace(':total', projects.length)"></p>
        @endif

        <ul class="grid gap-6 md:grid-cols-2">
            @foreach ($projects as $project)
                <li class="reveal" x-show="matches(@js($project->integrations ?? []))">
                    <x-site.project-card :project="$project" />
                </li>
            @endforeach
        </ul>

        <p x-cloak x-show="visibleCount === 0" class="text-muted">{{ __('site.projects.none') }}</p>
    </div>
</x-site.section>
