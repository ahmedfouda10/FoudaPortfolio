<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\IpHasher;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(IpHasher::class, fn (): IpHasher => new IpHasher((string) config('app.key')));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureModels();
        $this->configureDates();
        $this->configureDatabase();
    }

    /**
     * Outside production, fail loudly on lazy loading (N+1), on silently
     * discarded attributes and on access to attributes that were not loaded.
     */
    private function configureModels(): void
    {
        Model::shouldBeStrict(! $this->app->isProduction());
    }

    private function configureDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    /**
     * Block `migrate:fresh`, `db:wipe` and friends on production.
     */
    private function configureDatabase(): void
    {
        DB::prohibitDestructiveCommands($this->app->isProduction());
    }
}
