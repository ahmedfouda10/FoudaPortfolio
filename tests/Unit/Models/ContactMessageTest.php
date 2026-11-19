<?php

declare(strict_types=1);

use App\Enums\ContactInquiryType;
use App\Enums\ContactMessageStatus;
use App\Models\ContactMessage;

it('starts as a new message', function (): void {
    $message = ContactMessage::query()->create([
        'name' => 'Jane Recruiter',
        'email' => 'jane@example.com',
        'inquiry_type' => ContactInquiryType::JobOpportunity,
        'subject' => 'Backend role',
        'message' => 'Hello!',
    ]);

    expect($message->status)->toBe(ContactMessageStatus::New)
        ->and($message->fresh()?->status)->toBe(ContactMessageStatus::New);
});

it('returns only new messages from unread()', function (): void {
    $new = ContactMessage::factory()->create();
    ContactMessage::factory()->withStatus(ContactMessageStatus::Read)->create();
    ContactMessage::factory()->withStatus(ContactMessageStatus::Archived)->create();

    expect(ContactMessage::query()->unread()->pluck('id')->all())->toBe([$new->id]);
});
