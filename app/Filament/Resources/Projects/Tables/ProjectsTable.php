<?php

declare(strict_types=1);

namespace App\Filament\Resources\Projects\Tables;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

final class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('cover_image')->disk('public')->imageHeight(40),
                TextColumn::make('title')
                    ->description(fn (Project $record): ?string => $record->subtitle)
                    ->searchable(['title', 'subtitle', 'company']),
                TextColumn::make('status')->badge()->placeholder('—'),
                TextColumn::make('integrations')->badge()->separator(',')->limitList(3),
                TextColumn::make('views_count')->numeric()->sortable(),
                ToggleColumn::make('is_featured'),
                ToggleColumn::make('is_published'),
            ])
            ->filters([
                SelectFilter::make('status')->options(ProjectStatus::class),
                TernaryFilter::make('is_published'),
                TernaryFilter::make('is_featured'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
