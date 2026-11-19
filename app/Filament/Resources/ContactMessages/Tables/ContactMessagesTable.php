<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactMessages\Tables;

use App\Enums\ContactInquiryType;
use App\Enums\ContactMessageStatus;
use App\Filament\Resources\ContactMessages\ContactMessageActions;
use App\Models\ContactMessage;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

final class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('status')->badge(),
                TextColumn::make('name')
                    ->description(fn (ContactMessage $record): string => $record->email)
                    ->weight(fn (ContactMessage $record): ?FontWeight => $record->status === ContactMessageStatus::New ? FontWeight::Bold : null)
                    ->searchable(['name', 'email']),
                TextColumn::make('subject')->limit(60)->searchable(),
                TextColumn::make('inquiry_type')->badge(),
                TextColumn::make('created_at')->since()->dateTimeTooltip()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(ContactMessageStatus::class),
                SelectFilter::make('inquiry_type')->options(ContactInquiryType::class),
            ])
            ->recordActions([
                ViewAction::make(),
                ActionGroup::make([
                    ContactMessageActions::replyByEmail(),
                    ContactMessageActions::markAsRead(),
                    ContactMessageActions::markAsReplied(),
                    ContactMessageActions::archive(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ContactMessageActions::bulkMarkAs(ContactMessageStatus::Read),
                    ContactMessageActions::bulkMarkAs(ContactMessageStatus::Archived),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
