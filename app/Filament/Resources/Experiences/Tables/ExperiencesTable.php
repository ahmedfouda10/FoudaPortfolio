<?php

declare(strict_types=1);

namespace App\Filament\Resources\Experiences\Tables;

use App\Models\Experience;
use App\Support\Period;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

final class ExperiencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('role')
                    ->description(fn (Experience $record): string => $record->company)
                    ->searchable(['role', 'company']),
                TextColumn::make('period')
                    ->state(fn (Experience $record): string => Period::format($record->start_date, $record->end_date)),
                TextColumn::make('employment_type')->badge()->placeholder('—'),
                ToggleColumn::make('is_visible'),
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
