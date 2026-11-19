<?php

declare(strict_types=1);

use App\Enums\ContactMessageStatus;
use App\Filament\Widgets\PortfolioStatsOverview;
use App\Filament\Widgets\TopProjectsTable;
use App\Models\ContactMessage;
use App\Models\CvDownload;
use App\Models\Project;
use Livewire\Livewire;

beforeEach(fn () => actingAsAdmin());

it('shows message and CV download stats', function (): void {
    $this->travelTo(now()->setDate(2026, 11, 19));

    ContactMessage::factory()->count(2)->create();
    ContactMessage::factory()->withStatus(ContactMessageStatus::Replied)->create();
    ContactMessage::factory()->create(['created_at' => now()->subMonths(2), 'status' => ContactMessageStatus::Archived]);

    CvDownload::factory()->count(5)->create(['created_at' => now()->subDays(3)]);
    CvDownload::factory()->create(['created_at' => now()->subDays(45)]);

    Livewire::withoutLazyLoading()
        ->test(PortfolioStatsOverview::class)
        ->assertSeeInOrder(['New messages', '2', 'Messages this month', '3', 'CV downloads', '5', 'Last 30 days']);
});

it('lists the five most viewed projects', function (): void {
    $projects = collect([10, 60, 30, 50, 40, 20])->map(
        fn (int $views): Project => Project::factory()->create(['views_count' => $views]),
    );

    $top = $projects->sortByDesc('views_count')->take(5)->values();

    Livewire::withoutLazyLoading()
        ->test(TopProjectsTable::class)
        ->assertCanSeeTableRecords($top, inOrder: true)
        ->assertCanNotSeeTableRecords([$projects->firstWhere('views_count', 10)]);
});
