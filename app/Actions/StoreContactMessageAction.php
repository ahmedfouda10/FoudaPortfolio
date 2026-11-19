<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\ContactMessageData;
use App\Enums\ContactMessageStatus;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\NewContactMessage;
use App\Support\IpHasher;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

/**
 * Stores a contact-form message, then queues the admin notification and the
 * optional auto-reply. Only a keyed hash of the sender's IP is kept.
 */
final readonly class StoreContactMessageAction
{
    public function __construct(private IpHasher $ipHasher) {}

    public function __invoke(ContactMessageData $data, ?string $ip, ?string $userAgent): ContactMessage
    {
        $message = ContactMessage::query()->create([
            'name' => $data->name,
            'email' => $data->email,
            'inquiry_type' => $data->inquiryType,
            'budget' => $data->inquiryType->acceptsBudget() ? $data->budget : null,
            'subject' => $data->subject,
            'message' => $data->message,
            'status' => ContactMessageStatus::New,
            'ip_hash' => $this->ipHasher->hash($ip),
            'user_agent' => $userAgent === null ? null : Str::limit($userAgent, 509),
        ]);

        $this->notifyAdmin($message);

        if (config('portfolio.contact_form.auto_reply') === true) {
            Mail::to($message->email)->queue(new ContactMessageReceived);
        }

        return $message;
    }

    /**
     * The dashboard user (ADMIN_EMAIL) is notified; the public contact email
     * from the settings is the fallback.
     */
    private function notifyAdmin(ContactMessage $message): void
    {
        $admin = User::query()->where('email', config('portfolio.admin.email'))->first();

        if ($admin !== null) {
            $admin->notify(new NewContactMessage($message));

            return;
        }

        $fallback = SiteSetting::current()->email;

        if (filled($fallback)) {
            Notification::route('mail', $fallback)->notify(new NewContactMessage($message));
        }
    }
}
