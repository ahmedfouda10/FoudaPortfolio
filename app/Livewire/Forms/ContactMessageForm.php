<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Data\ContactMessageData;
use App\Enums\ContactInquiryType;
use Illuminate\Validation\Rule;
use Livewire\Form;

/**
 * Validation for the contact form: Livewire's counterpart of a Form Request.
 */
final class ContactMessageForm extends Form
{
    public string $name = '';

    public string $email = '';

    public string $inquiryType = ContactInquiryType::JobOpportunity->value;

    public string $budget = '';

    public string $subject = '';

    public string $message = '';

    public function acceptsBudget(): bool
    {
        return ContactInquiryType::tryFrom($this->inquiryType)?->acceptsBudget() ?? false;
    }

    /**
     * Validates the input and returns it as a typed object.
     */
    public function toData(): ContactMessageData
    {
        $this->validate();

        return new ContactMessageData(
            name: trim($this->name),
            email: trim($this->email),
            inquiryType: ContactInquiryType::from($this->inquiryType),
            budget: $this->acceptsBudget() ? trim($this->budget) : null,
            subject: trim($this->subject),
            message: trim($this->message),
        );
    }

    /**
     * @return array<string, list<mixed>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'inquiryType' => ['required', Rule::enum(ContactInquiryType::class)],
            'budget' => [Rule::requiredIf($this->acceptsBudget()), 'nullable', 'string', 'max:100'],
            'subject' => ['required', 'string', 'min:3', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'name' => (string) __('contact.attributes.name'),
            'email' => (string) __('contact.attributes.email'),
            'inquiryType' => (string) __('contact.attributes.inquiry_type'),
            'budget' => (string) __('contact.attributes.budget'),
            'subject' => (string) __('contact.attributes.subject'),
            'message' => (string) __('contact.attributes.message'),
        ];
    }
}
