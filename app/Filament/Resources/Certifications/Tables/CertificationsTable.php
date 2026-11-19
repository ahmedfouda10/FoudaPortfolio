<?php

declare(strict_types=1);

namespace App\Filament\Resources\Certifications\Tables;

use App\Models\Certification;
use App\Support\Period;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

final class CertificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('title')
                    ->description(fn (Certification $record): ?string => $record->issuer)
                    ->searchable(['title', 'issuer']),
                TextColumn::make('period')
                    ->state(fn (Certification $record): string => $record->is_in_progress
                        ? __('common.in_progress')
                        : Period::format($record->started_on, $record->completed_on)),
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
