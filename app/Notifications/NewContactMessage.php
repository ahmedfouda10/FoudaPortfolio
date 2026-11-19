<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Tells the site owner about a new contact-form message. Replying to the
 * email goes straight to the sender.
 */
final class NewContactMessage extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly ContactMessage $contactMessage)
    {
        $this->afterCommit();
    }

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = $this->contactMessage;

        return (new MailMessage)
            ->subject(__('mail.new_contact_message.subject', [
                'type' => $message->inquiry_type->getLabel(),
                'subject' => $message->subject,
            ]))
            ->replyTo($message->email, $message->name)
            ->markdown('mail.contact.new-message', [
                'contactMessage' => $message,
                'url' => ContactMessageResource::getUrl('view', ['record' => $message], panel: 'admin'),
            ]);
    }
}
