<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ProjectLinkType;
use App\Enums\ProjectStatus;
use App\Models\Project;
use Database\Seeders\Support\PlaceholderCover;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

final class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->projects() as $index => $project) {
            if (Project::query()->where('slug', $project['slug'])->exists()) {
                continue;
            }

            Project::query()->create([
                ...$project,
                ...$this->cover($project['slug'], $project['title']),
                'is_published' => true,
                'sort_order' => $index + 1,
            ]);
        }
    }

    /**
     * Use the bundled screenshot of the live website when there is one,
     * otherwise generate a neutral placeholder. Both are replaceable from the dashboard.
     *
     * @return array{cover_image: string, cover_image_alt: string}
     */
    private function cover(string $slug, string $title): array
    {
        $screenshot = database_path("seeders/images/projects/{$slug}.webp");

        if (! File::exists($screenshot)) {
            return [
                'cover_image' => PlaceholderCover::store($slug, $title),
                'cover_image_alt' => "{$title} — placeholder cover",
            ];
        }

        $path = "projects/covers/{$slug}.webp";
        Storage::disk('public')->put($path, File::get($screenshot));

        return [
            'cover_image' => $path,
            'cover_image_alt' => "Screenshot of the {$title} website",
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function projects(): array
    {
        return [
            [
                'title' => '2Loyal',
                'slug' => '2loyal',
                'subtitle' => 'Multi-Platform Loyalty & Rewards Platform',
                'tagline' => 'Turn one-time buyers into lifelong customers.',
                'summary' => 'A loyalty and rewards platform that helps online merchants turn first-time buyers into repeat customers with points, referrals, customer segmentation and automated campaigns, across Shopify, Salla, Magento and ERPNext.',
                'company' => 'Croco IT',
                'year' => 2026,
                'status' => ProjectStatus::Live,
                'is_featured' => true,
                'problem' => 'Online merchants lose customers after the first purchase and lack an easy way to reward repeat buyers across different e-commerce platforms.',
                'my_role' => 'Designed and built the product end-to-end — architecture, database, backend, all platform integrations, and the Shopify & Salla app-store submissions.',
                'solution' => <<<'HTML'
                    <ul>
                        <li>Points for purchases, reviews, sign-ups, birthdays and referrals, redeemed as checkout discount coupons.</li>
                        <li>Referral program with unique referral links.</li>
                        <li><strong>RFM customer segmentation</strong> (Champions, Loyalists, At-Risk, Lost).</li>
                        <li>Automated WhatsApp, SMS and email campaigns: welcome, birthdays, point-expiry reminders and win-back.</li>
                        <li>Custom loyalty widget and webhooks.</li>
                        <li>Analytics on points, active customers, rewards, referrals and campaign ROI.</li>
                    </ul>
                    HTML,
                'results' => '<p>Live and approved on the Shopify App Store (launched February 2026) and on the Salla App Store.</p>',
                'tech_stack' => ['Laravel', 'MySQL', 'REST APIs', 'Webhooks', 'Queues'],
                'integrations' => ['Shopify', 'Salla', 'Magento', 'ERPNext', 'Zid (in progress)'],
                'links' => [
                    ['label' => 'Website', 'url' => 'https://2loyal.net/', 'type' => ProjectLinkType::Website->value],
                    ['label' => 'Shopify App Store', 'url' => 'https://apps.shopify.com/2loyal', 'type' => ProjectLinkType::ShopifyApp->value],
                    ['label' => 'Salla App Store', 'url' => 'https://apps.salla.sa/en/app/1282695161', 'type' => ProjectLinkType::SallaApp->value],
                ],
                'meta_description' => '2Loyal: a multi-platform loyalty and rewards platform for Shopify, Salla, Magento and ERPNext merchants, designed and built end-to-end by Ahmed Fouda.',
            ],
            [
                'title' => 'Conte',
                'slug' => 'conte',
                'subtitle' => 'AI Product Content Generator',
                'tagline' => 'AI-powered app that generates product descriptions, titles, tags and images in bulk.',
                'summary' => 'A Shopify app that uses AI to generate product descriptions, SEO metadata, tags and product images for whole catalogs, with review before anything is published.',
                'company' => 'Croco IT',
                'year' => 2026,
                'status' => ProjectStatus::Live,
                'is_featured' => true,
                'problem' => 'Stores with large catalogs spend hours writing product descriptions and SEO metadata by hand.',
                'my_role' => 'Designed and built the product end-to-end — architecture, database, backend, OpenAI integration, and the Shopify app and its App Store approval.',
                'solution' => <<<'HTML'
                    <ul>
                        <li>AI-generated product descriptions, SEO titles, meta descriptions, bullet points and tags.</li>
                        <li>Custom templates per business domain.</li>
                        <li>Bulk processing for the whole catalog.</li>
                        <li>AI product image generation, reviewed before publishing to Shopify.</li>
                        <li>Multi-language output.</li>
                        <li>Credit-based subscription plans.</li>
                    </ul>
                    HTML,
                'results' => '<p>Live and approved on the Shopify App Store (launched March 2026).</p>',
                'tech_stack' => ['Laravel', 'MySQL', 'OpenAI API', 'REST APIs', 'Queues', 'Caching'],
                'integrations' => ['Shopify', 'OpenAI API'],
                'links' => [
                    ['label' => 'Website', 'url' => 'https://conte.artrixx.com/', 'type' => ProjectLinkType::Website->value],
                    ['label' => 'Shopify App Store', 'url' => 'https://apps.shopify.com/conte', 'type' => ProjectLinkType::ShopifyApp->value],
                ],
                'meta_description' => 'Conte: an AI product content generator for Shopify that writes descriptions, SEO metadata and tags and generates product images in bulk.',
            ],
            [
                'title' => 'SPC Academy',
                'slug' => 'spc-academy',
                'subtitle' => 'Online Medical & Dental Learning Platform',
                'tagline' => 'Expert-led medical and dental courses online.',
                'summary' => 'An online learning platform for medical and dental courses, with video lessons, clinical case studies, quizzes and an English and Arabic interface.',
                'company' => 'Croco IT',
                'year' => null, // TODO: add the year from the dashboard.
                'status' => ProjectStatus::Live,
                'is_featured' => false,
                'problem' => null, // TODO: describe the problem from the dashboard.
                // TODO: add the specific features you built (e.g. enrollments, payments, video protection, instructor tools).
                'my_role' => 'Designed and built the platform end-to-end.',
                'solution' => <<<'HTML'
                    <ul>
                        <li>Video courses, clinical case studies and quizzes.</li>
                        <li>Course bundles and specializations.</li>
                        <li>Student accounts.</li>
                        <li>English and Arabic interface.</li>
                    </ul>
                    HTML,
                // TODO: the public site shows 900+ active enrollees; confirm before adding it as a result.
                'results' => null,
                'tech_stack' => ['Laravel', 'MySQL', 'REST APIs'],
                'integrations' => [],
                'links' => [
                    ['label' => 'Website', 'url' => 'https://online.spc-eg.com/', 'type' => ProjectLinkType::Website->value],
                ],
                'meta_description' => 'SPC Academy: an online medical and dental learning platform with video courses, case studies and quizzes, built with Laravel.',
            ],
            [
                'title' => 'Tracking',
                'slug' => 'tracking',
                'subtitle' => 'Customer Behavior Analytics Platform',
                'tagline' => 'Customer behavior tracking, segmentation and automated campaigns.',
                'summary' => 'Customer behavior tracking with segmentation algorithms and interest analytics, automated email campaigns and SMS notifications, and a reporting dashboard.',
                'company' => null, // TODO: add the company (if it can be shown) from the dashboard.
                'year' => null, // TODO: add the year from the dashboard.
                'status' => null, // TODO: set the status from the dashboard.
                'is_featured' => false,
                'problem' => null, // TODO: describe the problem from the dashboard.
                'my_role' => null, // TODO: describe your role from the dashboard.
                'solution' => <<<'HTML'
                    <ul>
                        <li>Customer behavior tracking with segmentation algorithms and interest analytics.</li>
                        <li>Automated email campaigns and SMS notifications.</li>
                        <li>Reporting dashboard.</li>
                    </ul>
                    HTML,
                'results' => null,
                'tech_stack' => ['Laravel', 'MySQL', 'Email API', 'SMS gateway'],
                'integrations' => [],
                'links' => [],
                'meta_description' => 'Tracking: a customer behavior analytics platform with segmentation, interest analytics and automated email and SMS campaigns.',
            ],
        ];
    }
}
