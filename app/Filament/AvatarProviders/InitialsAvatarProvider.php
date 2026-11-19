<?php

declare(strict_types=1);

namespace App\Filament\AvatarProviders;

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
        $initials = str(Filament::getNameForDefaultAvatar($record))
            ->trim()
            ->explode(' ')
            ->map(fn (string $segment): string => mb_strtoupper(mb_substr((string) preg_replace('/^[^\p{L}\p{N}]+/u', '', $segment), 0, 1)))
            ->filter()
            ->take(2)
            ->join('');

        $text = htmlspecialchars($initials, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        $svg = <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64"><rect width="64" height="64" fill="#4F46E5"/><text x="50%" y="50%" fill="#FFFFFF" font-family="ui-sans-serif, system-ui, sans-serif" font-size="26" font-weight="600" text-anchor="middle" dominant-baseline="central">{$text}</text></svg>
            SVG;

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
