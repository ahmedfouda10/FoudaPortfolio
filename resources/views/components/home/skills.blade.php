@props(['skillGroups'])
<x-site.section id="skills" :eyebrow="__('site.sections.skills.eyebrow')" :title="__('site.sections.skills.title')">
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($skillGroups as $group)
            <div class="card reveal p-6">
                <h3 class="flex items-center gap-2 font-semibold">
                    @if (filled($group->icon) && array_key_exists($group->icon, \App\Filament\Support\HeroiconOptions::outline()))
                        <x-dynamic-component :component="$group->icon" class="size-5 text-accent" aria-hidden="true" />
                    @endif
                    {{ $group->name }}
                </h3>
                <ul class="mt-4 flex flex-wrap gap-2">
                    @foreach ($group->skills as $skill)
                        <li class="chip">{{ $skill->name }}</li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</x-site.section>
