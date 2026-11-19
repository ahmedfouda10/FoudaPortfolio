<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProjectLinkType: string implements HasLabel
{
    case Website = 'website';
    case ShopifyApp = 'shopify_app';
    case SallaApp = 'salla_app';
    case GitHub = 'github';
    case Other = 'other';

    public function getLabel(): string
    {
        return __("enums.project_link_type.{$this->value}");
    }

    /**
     * Whether the link points to an app-store listing rather than a website.
     */
    public function isAppStore(): bool
    {
        return in_array($this, [self::ShopifyApp, self::SallaApp], true);
    }
}
