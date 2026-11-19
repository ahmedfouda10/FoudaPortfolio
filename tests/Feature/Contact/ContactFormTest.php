<?php

declare(strict_types=1);

use App\Enums\ContactInquiryType;
use App\Enums\ContactMessageStatus;
use App\Livewire\ContactForm;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\NewContactMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

beforeEach(function (): void {
    Mail::fake();
    Notification::fake();
    config(['portfolio.admin.email' => 'owner@example.com', 'portfolio.contact_form.auto_reply' => true]);
    SiteSetting::factory()->create();
});

/**
 * @param  array<string, string>  $overrides
 * @return array<string, string>
 */
function validContactInput(array $overrides = []): array
{
    return [
        'form.name' => 'Jane Recruiter',
        'form.email' => 'jane@example.com',
        'form.inquiryType' => ContactInquiryType::JobOpportunity->value,
        'form.subject' => 'Senior Laravel role',
        'form.message' => 'We would love to talk to you about a remote backend role.',
        ...$overrides,
    ];
}

/**
 * Fill in the form and wait long enough to pass the time-to-submit check.
 *
 * @param  array<string, string>  $input
 */
function submitContactForm(array $input = []): Testable
{
    $component = Livewire::test(ContactForm::class)->set(validContactInput($input));

    test()->travel(5)->seconds();

    return $component->call('submit');
}

it('is rendered on the home page', function (): void {
    $this->get('/')->assertOk()->assertSeeLivewire(ContactForm::class);
});

it('validates the input with clear errors', function (): void {
    Livewire::test(ContactForm::class)
        ->set([
            'form.name' => '',
            'form.email' => 'not-an-email',
            'form.inquiryType' => 'spam',
            'form.subject' => '',
            'form.message' => 'too short',
        ])
        ->call('submit')
        ->assertHasErrors([
            'form.name' => 'required',
            'form.email' => 'email',
            'form.inquiryType',
            'form.subject' => 'required',
            'form.message' => 'min',
        ])
        ->assertSet('sent', false);

    expect(ContactMessage::query()->count())->toBe(0);
});

it('only shows and requires the budget for freelance projects', function (): void {
    Livewire::test(ContactForm::class)
        ->assertDontSee(__('contact.fields.budget'))
        ->set('form.inquiryType', ContactInquiryType::FreelanceProject->value)
        ->assertSee(__('contact.fields.budget'))
        ->set(validContactInput(['form.inquiryType' => ContactInquiryType::FreelanceProject->value, 'form.budget' => '']))
        ->call('submit')
        ->assertHasErrors(['form.budget' => 'required']);

    submitContactForm(['form.inquiryType' => ContactInquiryType::JobOpportunity->value, 'form.budget' => '$10,000'])
        ->assertHasNoErrors();

    expect(ContactMessage::query()->sole()->budget)->toBeNull();
});

it('stores a freelance inquiry with its budget', function (): void {
    submitContactForm([
        'form.inquiryType' => ContactInquiryType::FreelanceProject->value,
        'form.budget' => '$2,000 – $5,000',
    ])->assertHasNoErrors()->assertSet('sent', true);

    expect(ContactMessage::query()->sole())
        ->inquiry_type->toBe(ContactInquiryType::FreelanceProject)
        ->budget->toBe('$2,000 – $5,000');
});

it('can be pre-selected for freelance work from a link', function (): void {
    Livewire::withQueryParams(['inquiry' => 'freelance_project'])
        ->test(ContactForm::class)
        ->assertSet('form.inquiryType', ContactInquiryType::FreelanceProject->value)
        ->assertSee(__('contact.fields.budget'));
});

it('stores the message with a hashed IP and queues the notifications', function (): void {
    $admin = User::factory()->create(['email' => 'owner@example.com']);

    submitContactForm()->assertHasNoErrors()->assertSet('sent', true)->assertSee(__('contact.success.title'));

    $message = ContactMessage::query()->sole();

    expect($message)
        ->name->toBe('Jane Recruiter')
        ->email->toBe('jane@example.com')
        ->status->toBe(ContactMessageStatus::New)
        ->ip_hash->toBe(hash_hmac('sha256', '127.0.0.1', (string) config('app.key')));

    Notification::assertSentTo($admin, NewContactMessage::class, fn (NewContactMessage $notification): bool => $notification->contactMessage->is($message));
    Mail::assertQueued(ContactMessageReceived::class, fn (ContactMessageReceived $mail): bool => $mail->hasTo('jane@example.com'));
});

it('falls back to the public contact email when the admin user does not exist', function (): void {
    SiteSetting::current()->update(['email' => 'hello@example.com']);

    submitContactForm()->assertHasNoErrors();

    Notification::assertSentOnDemand(
        NewContactMessage::class,
        fn (NewContactMessage $notification, array $channels, object $notifiable): bool => $notifiable->routes['mail'] === 'hello@example.com',
    );
});

it('does not send the auto-reply when it is disabled', function (): void {
    config(['portfolio.contact_form.auto_reply' => false]);

    submitContactForm()->assertHasNoErrors();

    Mail::assertNothingQueued();
});

it('silently drops submissions that fill in the honeypot', function (): void {
    Livewire::test(ContactForm::class)
        ->set(validContactInput())
        ->set('website', 'https://spam.example')
        ->call('submit')
        ->assertSet('sent', true);

    expect(ContactMessage::query()->count())->toBe(0);
    Notification::assertNothingSent();
    Mail::assertNothingQueued();
});

it('rejects submissions sent faster than a person could type', function (): void {
    Livewire::test(ContactForm::class)
        ->set(validContactInput())
        ->call('submit')
        ->assertHasErrors(['form'])
        ->assertSet('sent', false);

    expect(ContactMessage::query()->count())->toBe(0);
});

it('rate limits submissions per visitor', function (): void {
    foreach (range(1, 3) as $attempt) {
        submitContactForm(['form.subject' => "Message {$attempt}"])->assertHasNoErrors();
    }

    submitContactForm(['form.subject' => 'Message 4'])
        ->assertHasErrors(['form'])
        ->assertSee(__('contact.errors.rate_limited', ['minutes' => 10]));

    expect(ContactMessage::query()->count())->toBe(3);

    $this->travel(11)->minutes();

    submitContactForm(['form.subject' => 'Message 5'])->assertHasNoErrors();
    expect(ContactMessage::query()->count())->toBe(4);
});

it('can start over after sending', function (): void {
    submitContactForm()
        ->assertSet('sent', true)
        ->call('startOver')
        ->assertSet('sent', false)
        ->assertSet('form.name', '')
        ->assertSee(__('contact.submit'));
});
