<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ContactInquiryType;
use App\Enums\ContactMessageStatus;
use Database\Factories\ContactMessageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name', 'email', 'inquiry_type', 'budget', 'subject', 'message',
    'status', 'ip_hash', 'user_agent',
])]
class ContactMessage extends Model
{
    /** @use HasFactory<ContactMessageFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => ContactMessageStatus::New->value,
    ];

    public function markAs(ContactMessageStatus $status): void
    {
        $this->update(['status' => $status]);
    }

    /**
     * The reply link opens the admin's mail client with the recipient and subject pre-filled.
     */
    public function replyMailtoUrl(): string
    {
        return 'mailto:'.rawurlencode($this->email).'?subject='.rawurlencode("Re: {$this->subject}");
    }

    /**
     * Messages that have not been opened in the dashboard yet.
     *
     * @param  Builder<static>  $query
     */
    #[Scope]
    protected function unread(Builder $query): void
    {
        $query->where($this->qualifyColumn('status'), ContactMessageStatus::New);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'inquiry_type' => ContactInquiryType::class,
            'status' => ContactMessageStatus::class,
        ];
    }
}
