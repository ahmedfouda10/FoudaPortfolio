<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Architecture rules
|--------------------------------------------------------------------------
|
| These tests keep the codebase honest as it grows. More specific rules
| (Actions are final, controllers do not query the database, …) are added
| alongside the layers they protect.
|
*/

arch('application code declares strict types')
    ->expect(['App', 'Database'])
    ->toUseStrictTypes();

arch('no debugging helpers are left behind')
    ->expect(['dd', 'ddd', 'dump', 'ray', 'var_dump', 'print_r', 'var_export', 'die', 'exit'])
    ->not->toBeUsed();

arch('env() is only called from config files')
    ->expect('env')
    ->not->toBeUsed();

arch('PHP security preset')
    ->preset()
    ->security();
