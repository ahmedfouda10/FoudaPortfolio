<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ContactInquiryType;
use App\Enums\ContactMessageStatus;
use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactMessage>
 */
class ContactMessageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'inquiry_type' => ContactInquiryType::JobOpportunity,
            'budget' => null,
            'subject' => fake()->sentence(5),
            'message' => fake()->paragraphs(2, true),
            'status' => ContactMessageStatus::New,
            'ip_hash' => hash('sha256', fake()->ipv4()),
            'user_agent' => fake()->userAgent(),
        ];
    }

    public function freelance(): static
    {
        return $this->state([
            'inquiry_type' => ContactInquiryType::FreelanceProject,
            'budget' => '$1,000 – $3,000',
        ]);
    }

    public function withStatus(ContactMessageStatus $status): static
    {
        return $this->state(['status' => $status]);
    }
}
