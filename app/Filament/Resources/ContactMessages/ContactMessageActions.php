<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactMessages;

use App\Enums\ContactMessageStatus;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Triage actions shared by the messages table and the message view page.
 */
final class ContactMessageActions
{
    public static function markAsRead(): Action
    {
        return self::statusAction('markAsRead', ContactMessageStatus::Read, Heroicon::OutlinedEnvelopeOpen);
    }

    public static function markAsReplied(): Action
    {
        return self::statusAction('markAsReplied', ContactMessageStatus::Replied, Heroicon::OutlinedCheckCircle);
    }

    public static function archive(): Action
    {
        return self::statusAction('archive', ContactMessageStatus::Archived, Heroicon::OutlinedArchiveBox);
    }

    public static function replyByEmail(): Action
    {
        return Action::make('replyByEmail')
            ->label(__('admin.actions.reply_by_email'))
            ->icon(Heroicon::OutlinedPaperAirplane)
            ->color('primary')
            ->url(fn (ContactMessage $record): string => $record->replyMailtoUrl());
    }

    public static function bulkMarkAs(ContactMessageStatus $status): BulkAction
    {
        return BulkAction::make("bulkMarkAs{$status->name}")
            ->label(__("admin.actions.mark_as.{$status->value}"))
            ->icon(Heroicon::OutlinedTag)
            ->deselectRecordsAfterCompletion()
            ->action(function (Collection $records) use ($status): void {
                foreach ($records as $record) {
                    if ($record instanceof ContactMessage) {
                        $record->markAs($status);
                    }
                }
            });
    }

    private static function statusAction(string $name, ContactMessageStatus $status, Heroicon $icon): Action
    {
        return Action::make($name)
            ->label(__("admin.actions.mark_as.{$status->value}"))
            ->icon($icon)
            ->color($status->getColor())
            ->visible(fn (ContactMessage $record): bool => $record->status !== $status)
            ->action(fn (ContactMessage $record) => $record->markAs($status))
            ->successNotificationTitle(__('admin.notifications.status_updated'));
    }
}
