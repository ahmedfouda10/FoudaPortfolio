@props(['status'])
@if ($status !== null)
    <span @class(['badge', 'badge-success' => $status === \App\Enums\ProjectStatus::Live, 'badge-warning' => $status !== \App\Enums\ProjectStatus::Live])>
        <span aria-hidden="true" class="size-1.5 rounded-full bg-current"></span>
        {{ $status->getLabel() }}
    </span>
@endif
