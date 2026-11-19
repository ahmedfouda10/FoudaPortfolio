<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

final class TopProjectsTable extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('admin.widgets.top_projects'))
            ->query(fn () => Project::query()->orderByDesc('views_count')->orderBy('id')->limit(5))
            ->paginated(false)
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('views_count')->numeric(),
                IconColumn::make('is_published')->boolean(),
            ]);
    }
}
