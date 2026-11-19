<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use App\Queries\CvDownloadsPerDay;
use Carbon\CarbonImmutable;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

final class PortfolioStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $cvDownloads = app(CvDownloadsPerDay::class)(30);

        return [
            Stat::make(__('admin.widgets.new_messages'), ContactMessage::query()->unread()->count())
                ->icon(Heroicon::OutlinedInbox)
                ->color('warning'),
            Stat::make(
                __('admin.widgets.messages_this_month'),
                ContactMessage::query()->where('created_at', '>=', CarbonImmutable::now()->startOfMonth())->count(),
            )->icon(Heroicon::OutlinedEnvelope),
            Stat::make(__('admin.widgets.cv_downloads'), array_sum($cvDownloads))
                ->description(__('admin.widgets.last_30_days'))
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->chart(array_map(fn (int $count): float => $count, array_values($cvDownloads)))
                ->color('success'),
        ];
    }
}
