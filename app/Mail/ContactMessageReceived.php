<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Auto-reply to the visitor. It intentionally contains no text typed by the
 * visitor, so the form cannot be abused to send arbitrary content to others.
 */
final class ContactMessageReceived extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct()
    {
        $this->afterCommit();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('mail.contact_received.subject', ['name' => SiteSetting::current()->full_name]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.contact.received',
            with: ['senderName' => SiteSetting::current()->full_name],
        );
    }
}
