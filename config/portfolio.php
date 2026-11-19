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

];
