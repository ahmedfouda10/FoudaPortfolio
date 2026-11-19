<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Enums\ContactMessageStatus;
use App\Filament\Resources\ContactMessages\ContactMessageActions;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

/**
 * @property ContactMessage $record
 */
final class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    /**
     * Opening a new message marks it as read.
     */
    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->record->status === ContactMessageStatus::New) {
            $this->record->markAs(ContactMessageStatus::Read);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            ContactMessageActions::replyByEmail(),
            ContactMessageActions::markAsReplied(),
            ContactMessageActions::archive(),
            DeleteAction::make(),
        ];
    }
}
