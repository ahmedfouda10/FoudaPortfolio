@props(['education', 'certifications'])
<x-site.section id="education" :eyebrow="__('site.sections.education.eyebrow')" :title="__('site.sections.education.title')" class="bg-surface/40">
    <div class="grid gap-10 lg:grid-cols-2">
        @if ($education->isNotEmpty())
            <div>
                <h3 class="flex items-center gap-2 text-lg font-semibold">
                    <x-heroicon-o-academic-cap class="size-5 text-accent" aria-hidden="true" />
                    {{ __('site.education.education') }}
                </h3>
                <ul class="mt-6 space-y-4">
                    @foreach ($education as $item)
                        <li class="card reveal p-6">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <p class="font-semibold">{{ $item->degree }}@if (filled($item->field)) <span class="text-muted">— {{ $item->field }}</span>@endif</p>
                                @if ($item->start_year || $item->end_year)
                                    <p class="font-mono text-sm text-muted">{{ collect([$item->start_year, $item->end_year])->filter()->implode(' – ') }}</p>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-accent">{{ $item->institution }}</p>
                            @if (filled($item->description))
                                <p class="mt-3 text-sm leading-relaxed text-muted">{{ $item->description }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($certifications->isNotEmpty())
            <div>
                <h3 class="flex items-center gap-2 text-lg font-semibold">
                    <x-heroicon-o-check-badge class="size-5 text-accent" aria-hidden="true" />
                    {{ __('site.education.certifications') }}
                </h3>
                <ul class="mt-6 space-y-4">
                    @foreach ($certifications as $certification)
                        <li class="card reveal p-6">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <p class="font-semibold">{{ $certification->title }}</p>
                                @if ($certification->is_in_progress)
                                    <span class="badge badge-warning">{{ __('site.education.in_progress') }}</span>
                                @else
                                    <p class="font-mono text-sm text-muted">{{ \App\Support\Period::format($certification->started_on, $certification->completed_on) }}</p>
                                @endif
                            </div>
                            @if (filled($certification->issuer))
                                <p class="mt-1 text-sm text-accent">{{ $certification->issuer }}</p>
                            @endif
                            @if (filled($certification->description))
                                <p class="mt-3 text-sm leading-relaxed text-muted">{{ $certification->description }}</p>
                            @endif
                            @if (filled($certification->credential_urls))
                                <ul class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-sm">
                                    @foreach ($certification->credential_urls as $url)
                                        <li>
                                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="link inline-flex min-h-8 items-center gap-1">
                                                {{ count($certification->credential_urls) > 1 ? __('site.education.view_credential_n', ['number' => $loop->iteration]) : __('site.education.view_credential') }}
                                                <x-heroicon-m-arrow-top-right-on-square class="size-3.5" aria-hidden="true" />
                                                <span class="sr-only">{{ __('site.project.opens_in_new_tab') }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</x-site.section>
