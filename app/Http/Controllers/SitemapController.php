<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Queries\GetSitemapUrls;
use Illuminate\Http\Response;

final class SitemapController extends Controller
{
    public function __invoke(GetSitemapUrls $urls): Response
    {
        return response()
            ->view('seo.sitemap', ['urls' => $urls()])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
