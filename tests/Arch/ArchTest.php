<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;

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

arch('enums are string backed')
    ->expect('App\Enums')
    ->toBeStringBackedEnums();

arch('models extend Eloquent')
    ->expect('App\Models')
    ->classes()
    ->toExtend(Model::class);

arch('model concerns are traits')
    ->expect('App\Models\Concerns')
    ->toBeTraits();

arch('seeders are final')
    ->expect('Database\Seeders')
    ->classes()
    ->toBeFinal();

arch('Filament classes, Livewire components, queries and middleware are final')
    ->expect(['App\\Filament', 'App\\Livewire', 'App\\Queries', 'App\\Http\\Middleware', 'App\\Support', 'App\\Notifications', 'App\\Mail'])
    ->classes()
    ->toBeFinal();

arch('controllers stay thin and never touch the query builder directly')
    ->expect('App\\Http\\Controllers')
    ->not->toUse(['Illuminate\\Support\\Facades\\DB', 'Illuminate\\Database\\Query\\Builder']);

arch('actions are final, invokable classes')
    ->expect('App\\Actions')
    ->classes()
    ->toBeFinal()
    ->toHaveSuffix('Action')
    ->toHaveMethod('__invoke');

arch('emails and notifications are always queued')
    ->expect(['App\\Notifications', 'App\\Mail'])
    ->classes()
    ->toImplement(Illuminate\Contracts\Queue\ShouldQueue::class);
