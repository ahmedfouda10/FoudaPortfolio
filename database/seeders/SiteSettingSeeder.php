<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

final class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        if (SiteSetting::query()->exists()) {
            return;
        }

        SiteSetting::query()->create([
            'full_name' => 'Ahmed Fouda',
            'headline' => 'Senior Backend Developer — PHP · Laravel · E-commerce Integrations',
            'short_bio' => 'Backend developer with 4+ years of experience building scalable web applications and APIs with PHP and Laravel, specialized in e-commerce and third-party integrations.',
            'location' => 'Kafr El-Sheikh, Egypt (open to remote)',
            'availability_status' => 'Open to remote roles & freelance projects',

            // Private details come from .env (or are filled in from the dashboard).
            'email' => config('portfolio.contact.email'),
            'phone' => config('portfolio.contact.phone'),
            'whatsapp' => config('portfolio.contact.whatsapp'),

            'linkedin_url' => 'https://www.linkedin.com/in/ahmed-fouda-9497a9200/',
            'github_url' => 'https://github.com/ahmedfouda10',
            'freelancer_url' => 'https://www.freelancer.com/u/AhmedFouda10',
            'source_code_url' => 'https://github.com/ahmedfouda10/FoudaPortfolio',

            'years_of_experience' => 4,
            'projects_shipped' => null, // TODO: set the real number from the dashboard.
            'platforms_integrated' => null, // TODO: set the real number from the dashboard.

            'about_body' => <<<'HTML'
                <p>Backend Developer with 4+ years of professional experience building scalable web applications and APIs with PHP 8+ and Laravel, following OOP and clean-code practices.</p>
                <p>Specialized in third-party and e-commerce integrations: built and published apps approved on the Shopify App Store and the Salla App Store, and connected business systems with Magento and ERPNext.</p>
                <p>Strong in database design and query optimization, REST and GraphQL APIs, and AI-powered features using the OpenAI API. Solid front-end knowledge (JavaScript, jQuery, Bootstrap) that helps me deliver complete features and work closely with front-end teams.</p>
                HTML,
            'about_highlights' => [
                'Apps approved on the Shopify App Store and the Salla App Store',
                'Integrations with Shopify, Salla, Magento and ERPNext',
                'REST & GraphQL APIs, database design and query optimization',
                'AI-powered features with the OpenAI API',
            ],

            'meta_title' => 'Ahmed Fouda — Senior Backend Developer (PHP · Laravel)',
            'meta_description' => 'Senior Backend Developer specialized in PHP, Laravel and e-commerce integrations: Shopify & Salla apps, Magento and ERPNext integrations, REST & GraphQL APIs.',
        ]);
    }
}
