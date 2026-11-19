<?php

declare(strict_types=1);

use App\Enums\ContactMessageStatus;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Filament\Resources\ContactMessages\Pages\ViewContactMessage;
use App\Models\ContactMessage;
use Livewire\Livewire;

beforeEach(fn () => actingAsAdmin());

it('lists messages, newest first', function (): void {
    $older = ContactMessage::factory()->create(['created_at' => now()->subDay()]);
    $newer = ContactMessage::factory()->create();

    Livewire::test(ListContactMessages::class)
        ->assertCanSeeTableRecords([$newer, $older], inOrder: true);
});

it('shows the number of new messages in the navigation badge', function (): void {
    expect(ContactMessageResource::getNavigationBadge())->toBeNull();

    ContactMessage::factory()->count(2)->create();
    ContactMessage::factory()->withStatus(ContactMessageStatus::Read)->create();

    expect(ContactMessageResource::getNavigationBadge())->toBe('2');
});

it('shows a message and marks it as read', function (): void {
    $message = ContactMessage::factory()->create(['subject' => 'Senior Laravel role', 'message' => 'Are you available?']);

    Livewire::test(ViewContactMessage::class, ['record' => $message->getRouteKey()])
        ->assertOk()
        ->assertSee('Senior Laravel role')
        ->assertSee('Are you available?');

    expect($message->refresh()->status)->toBe(ContactMessageStatus::Read);
});

it('changes the status from the table', function (string $action, ContactMessageStatus $expected): void {
    $message = ContactMessage::factory()->create();

    Livewire::test(ListContactMessages::class)->callTableAction($action, $message);

    expect($message->refresh()->status)->toBe($expected);
})->with([
    'mark as read' => ['markAsRead', ContactMessageStatus::Read],
    'mark as replied' => ['markAsReplied', ContactMessageStatus::Replied],
    'archive' => ['archive', ContactMessageStatus::Archived],
]);

it('marks several messages as read at once', function (): void {
    $messages = ContactMessage::factory()->count(3)->create();

    Livewire::test(ListContactMessages::class)
        ->callTableBulkAction('bulkMarkAsRead', $messages);

    expect(ContactMessage::query()->unread()->count())->toBe(0);
});

it('offers a reply link that opens the mail client', function (): void {
    $message = ContactMessage::factory()->create(['email' => 'jane@example.com', 'subject' => 'Hello there']);

    expect($message->replyMailtoUrl())->toBe('mailto:jane%40example.com?subject=Re%3A%20Hello%20there');

    Livewire::test(ViewContactMessage::class, ['record' => $message->getRouteKey()])
        ->assertActionHasUrl('replyByEmail', $message->replyMailtoUrl());
});

it('does not allow creating or editing messages', function (): void {
    $message = ContactMessage::factory()->create();

    expect(ContactMessageResource::canCreate())->toBeFalse()
        ->and(ContactMessageResource::canEdit($message))->toBeFalse();

    $this->get('/admin/contact-messages/create')->assertNotFound();
    $this->get("/admin/contact-messages/{$message->id}/edit")->assertNotFound();
});
