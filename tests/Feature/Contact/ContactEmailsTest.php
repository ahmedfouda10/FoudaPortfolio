<?php

declare(strict_types=1);

use App\Enums\ContactInquiryType;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\NewContactMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

beforeEach(fn () => SiteSetting::factory()->create(['full_name' => 'Ahmed Fouda']));

it('tells the owner about a new message and lets them reply to the sender', function (): void {
    $message = ContactMessage::factory()->freelance()->create([
        'name' => 'Jane Client',
        'email' => 'jane@example.com',
        'subject' => 'Shopify app',
        'message' => "Line one\n\n[click me](https://evil.example)\n<b>bold?</b>",
    ]);

    $mail = (new NewContactMessage($message))->toMail(User::factory()->make());
    $html = (string) $mail->render();

    expect($mail->subject)->toBe('New Freelance project message: Shopify app')
        ->and($mail->replyTo)->toBe([['jane@example.com', 'Jane Client']])
        ->and($html)
        ->toContain('Jane Client')
        ->toContain('$1,000 – $3,000')
        ->toContain('Line one<br><br>[click me](https://evil.example)<br>&lt;b&gt;bold?&lt;/b&gt;')
        ->not->toContain('<a href="https://evil.example"')
        ->toContain('/admin/contact-messages/'.$message->id);
});

it('sends a generic auto-reply that contains nothing the visitor typed', function (): void {
    $html = (new ContactMessageReceived)->render();

    expect($html)
        ->toContain(__('mail.contact_received.body'))
        ->toContain('Ahmed Fouda');

    expect((new ContactMessageReceived)->envelope()->subject)->toBe('Thanks for your message — Ahmed Fouda');
});

it('queues every email', function (): void {
    expect(new NewContactMessage(ContactMessage::factory()->make(['inquiry_type' => ContactInquiryType::Other])))->toBeInstanceOf(ShouldQueue::class)
        ->and(new ContactMessageReceived)->toBeInstanceOf(ShouldQueue::class);
});
