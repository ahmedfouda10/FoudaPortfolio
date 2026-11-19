<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Admin account
    |--------------------------------------------------------------------------
    |
    | The single dashboard user is created (or updated) by the AdminUserSeeder
    | from these values. They live in .env only and are never committed.
    |
    */

    'admin' => [
        'name' => env('ADMIN_NAME', 'Admin'),
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),

        // Force the admin to set up app-based MFA (authenticator app) before using the dashboard.
        'require_mfa' => (bool) env('ADMIN_REQUIRE_MFA', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Private contact details
    |--------------------------------------------------------------------------
    |
    | Used once by the SiteSettingSeeder to pre-fill the settings row. After
    | that, they are managed from the dashboard. Keep them out of the repo.
    |
    */

    'contact' => [
        'email' => env('CONTACT_EMAIL'),
        'phone' => env('CONTACT_PHONE'),
        'whatsapp' => env('CONTACT_WHATSAPP'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact form
    |--------------------------------------------------------------------------
    |
    | Spam protection (honeypot + minimum time to submit + rate limit per
    | hashed IP) and the optional auto-reply sent to the visitor.
    |
    */

    'contact_form' => [
        'auto_reply' => (bool) env('CONTACT_AUTO_REPLY', true),
        'min_seconds_to_submit' => 3,
        'max_submissions' => 3,
        'decay_minutes' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Trusted proxies
    |--------------------------------------------------------------------------
    |
    | Comma-separated IPs (or "*") of the load balancer / Cloudflare in front of
    | the app, so visitor IPs (rate limiting, hashing) and HTTPS are detected
    | correctly. Read through config so it also works with a cached config.
    |
    */

    'trusted_proxies' => env('TRUSTED_PROXIES'),

];
