<?php

declare(strict_types=1);

use App\Enums\ContactInquiryType;
use App\Enums\ContactMessageStatus;
use App\Enums\EmploymentType;
use App\Enums\ProjectLinkType;
use App\Enums\ProjectStatus;

it('has a translated label for every case', function (string $enum): void {
    foreach ($enum::cases() as $case) {
        expect($case->getLabel())
            ->toBeString()
            ->not->toBeEmpty()
            ->not->toStartWith('enums.');
    }
})->with([
    ContactInquiryType::class,
    ContactMessageStatus::class,
    EmploymentType::class,
    ProjectLinkType::class,
    ProjectStatus::class,
]);

it('only accepts a budget for freelance inquiries', function (): void {
    expect(ContactInquiryType::FreelanceProject->acceptsBudget())->toBeTrue()
        ->and(ContactInquiryType::JobOpportunity->acceptsBudget())->toBeFalse()
        ->and(ContactInquiryType::Other->acceptsBudget())->toBeFalse();
});

it('knows which links are app-store listings', function (): void {
    expect(ProjectLinkType::ShopifyApp->isAppStore())->toBeTrue()
        ->and(ProjectLinkType::SallaApp->isAppStore())->toBeTrue()
        ->and(ProjectLinkType::Website->isAppStore())->toBeFalse();
});
