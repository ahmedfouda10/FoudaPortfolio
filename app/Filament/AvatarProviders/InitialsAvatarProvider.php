<?php

declare(strict_types=1);

namespace App\Filament\AvatarProviders;

use App\Support\InitialsSvg;
use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

/**
 * Renders the user's initials as an inline SVG data URI, so the dashboard
 * never sends the admin's name to a third-party avatar service.
 */
final class InitialsAvatarProvider implements AvatarProvider
{
    public function get(Model|Authenticatable $record): string
    {
        return InitialsSvg::dataUri(Filament::getNameForDefaultAvatar($record));
    }
}
