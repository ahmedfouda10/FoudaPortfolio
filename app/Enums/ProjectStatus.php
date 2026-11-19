<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ProjectStatus: string implements HasColor, HasLabel
{
    case Live = 'live';
    case InProgress = 'in_progress';

    public function getLabel(): string
    {
        return __("enums.project_status.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Live => 'success',
            self::InProgress => 'warning',
        };
    }
}
