<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Feature tests boot the full Laravel application and run against a fresh
| database (SQLite in memory locally, MySQL in CI). Unit tests also get the
| application so models, enums and scopes can be tested in isolation.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

const ADMIN_EMAIL = 'owner@example.com';

/**
 * Log in as the single dashboard user (the one matching ADMIN_EMAIL).
 */
function actingAsAdmin(): User
{
    config(['portfolio.admin.email' => ADMIN_EMAIL]);

    $admin = User::factory()->create(['email' => ADMIN_EMAIL]);

    test()->actingAs($admin);

    return $admin;
}
