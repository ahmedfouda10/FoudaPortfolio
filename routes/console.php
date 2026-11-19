<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Scheduled tasks
|--------------------------------------------------------------------------
|
| Run by the server cron entry (see DEPLOYMENT.md):
|   * * * * * cd /var/www/portfolio && php artisan schedule:run >> /dev/null 2>&1
|
*/

// Keep the failed-jobs table small.
Schedule::command('queue:prune-failed', ['--hours' => 168])->daily();
