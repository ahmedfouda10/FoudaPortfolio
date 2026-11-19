<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ContactMessageStatus: string implements HasColor, HasLabel
{
    case New = 'new';
    case Read = 'read';
    case Replied = 'replied';
    case Archived = 'archived';

    public function getLabel(): string
    {
        return __("enums.contact_message_status.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::New => 'warning',
            self::Read => 'info',
            self::Replied => 'success',
            self::Archived => 'gray',
        };
    }
}
