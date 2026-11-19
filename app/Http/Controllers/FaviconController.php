<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Support\InitialsSvg;
use Illuminate\Http\Response;

final class FaviconController extends Controller
{
    public function __invoke(): Response
    {
        return response(InitialsSvg::make(SiteSetting::current()->full_name))
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'public, max-age=86400');
    }
}
