<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['Laravel Backend & API Development', 'heroicon-o-server-stack', 'Scalable backends and REST/GraphQL APIs for web and mobile apps.'],
            ['Shopify App Development', 'heroicon-o-shopping-bag', 'Public or custom Shopify apps, from OAuth and webhooks to App Store approval.'],
            ['Salla & Zid App Development', 'heroicon-o-building-storefront', 'Apps for Saudi e-commerce platforms, published on their app stores.'],
            ['E-commerce & ERP Integrations', 'heroicon-o-arrows-right-left', 'Syncing products, orders and customers between stores and systems like ERPNext and Magento.'],
            ['Admin Dashboards with Filament', 'heroicon-o-squares-2x2', 'Fast, clean internal tools and control panels.'],
            ['AI-Powered Features', 'heroicon-o-sparkles', 'Content and image generation with the OpenAI API, built into existing products.'],
        ];

        foreach ($services as $index => [$title, $icon, $description]) {
            Service::query()->firstOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'short_description' => $description,
                    'icon' => $icon,
                    'sort_order' => $index + 1,
                    'is_visible' => true,
                ],
            );
        }
    }
}
