<?php

declare(strict_types=1);

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
