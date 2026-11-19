<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ContactInquiryType: string implements HasColor, HasLabel
{
    case JobOpportunity = 'job_opportunity';
    case FreelanceProject = 'freelance_project';
    case Other = 'other';

    public function getLabel(): string
    {
        return __("enums.contact_inquiry_type.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::JobOpportunity => 'info',
            self::FreelanceProject => 'success',
            self::Other => 'gray',
        };
    }

    /**
     * A budget is only relevant (and only collected) for freelance work.
     */
    public function acceptsBudget(): bool
    {
        return $this === self::FreelanceProject;
    }
}
