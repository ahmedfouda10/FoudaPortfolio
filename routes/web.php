<?php

declare(strict_types=1);

use App\Http\Controllers\DownloadCvController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShowProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/projects/{slug}', ShowProjectController::class)
    ->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')
    ->name('projects.show');

Route::get('/cv', DownloadCvController::class)
    ->middleware('throttle:20,1')
    ->name('cv.download');
