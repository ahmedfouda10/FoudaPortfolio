<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Actions\StoreContactMessageAction;
use App\Enums\ContactInquiryType;
use App\Livewire\Forms\ContactMessageForm;
use App\Support\IpHasher;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Public contact form.
 *
 * Spam protection, in order: a honeypot field (bots get a fake success), then
 * after validation a minimum time between rendering and submitting, and a
 * rate limit per hashed IP.
 */
final class ContactForm extends Component
{
    public ContactMessageForm $form;

    /**
     * Honeypot: visually hidden and skipped by keyboard and screen readers.
     */
    public string $website = '';

    #[Locked]
    public int $renderedAt = 0;

    public bool $sent = false;

    public function mount(Request $request): void
    {
        $this->renderedAt = CarbonImmutable::now()->getTimestamp();

        $preselected = ContactInquiryType::tryFrom((string) $request->query('inquiry'));

        if ($preselected !== null) {
            $this->form->inquiryType = $preselected->value;
        }
    }

    public function submit(StoreContactMessageAction $storeMessage, IpHasher $ipHasher, Request $request): void
    {
        if ($this->website !== '') {
            $this->sent = true;

            return;
        }

        $data = $this->form->toData();

        if ($this->submittedTooFast()) {
            $this->addError('form', __('contact.errors.too_fast'));

            return;
        }

        $rateLimitKey = 'contact-form:'.$ipHasher->hash($request->ip());

        if (RateLimiter::tooManyAttempts($rateLimitKey, (int) config('portfolio.contact_form.max_submissions'))) {
            $this->addError('form', __('contact.errors.rate_limited', [
                'minutes' => (int) ceil(RateLimiter::availableIn($rateLimitKey) / 60),
            ]));

            return;
        }

        RateLimiter::hit($rateLimitKey, (int) config('portfolio.contact_form.decay_minutes') * 60);

        $storeMessage($data, $request->ip(), $request->userAgent());

        $this->form->reset();
        $this->sent = true;
    }

    public function startOver(): void
    {
        $this->resetErrorBag();
        $this->form->reset();
        $this->website = '';
        $this->sent = false;
        $this->renderedAt = CarbonImmutable::now()->getTimestamp();
    }

    public function render(): View
    {
        return view('livewire.contact-form', [
            'inquiryTypes' => ContactInquiryType::cases(),
        ]);
    }

    private function submittedTooFast(): bool
    {
        $elapsed = CarbonImmutable::now()->getTimestamp() - $this->renderedAt;

        return $elapsed < (int) config('portfolio.contact_form.min_seconds_to_submit');
    }
}
