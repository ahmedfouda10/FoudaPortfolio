<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Support\Facades\Route;

afterEach(fn () => TrustProxies::flushState());

it('uses the forwarded client IP only behind a configured proxy', function (): void {
    Route::get('/_ip', fn () => request()->ip());

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.5'])
        ->withHeader('X-Forwarded-For', '203.0.113.9')
        ->get('/_ip')
        ->assertSeeText('10.0.0.5');

    config(['portfolio.trusted_proxies' => '10.0.0.5']);
    (new AppServiceProvider(app()))->boot();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.5'])
        ->withHeader('X-Forwarded-For', '203.0.113.9')
        ->get('/_ip')
        ->assertSeeText('203.0.113.9');
});
