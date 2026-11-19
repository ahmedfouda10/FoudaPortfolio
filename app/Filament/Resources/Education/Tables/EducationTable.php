<?php

declare(strict_types=1);

namespace App\Filament\Resources\Education\Tables;

use App\Models\Education;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

final class EducationTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('degree')
                    ->description(fn (Education $record): string => $record->institution)
                    ->searchable(['degree', 'institution']),
                TextColumn::make('years')
                    ->state(fn (Education $record): string => collect([$record->start_year, $record->end_year])->filter()->implode(' – ')),
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
