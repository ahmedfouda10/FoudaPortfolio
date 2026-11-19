<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\ContactInquiryType;

/**
 * A validated contact-form submission.
 */
final readonly class ContactMessageData
{
    public function __construct(
        public string $name,
        public string $email,
        public ContactInquiryType $inquiryType,
        public ?string $budget,
        public string $subject,
        public string $message,
    ) {}
}
