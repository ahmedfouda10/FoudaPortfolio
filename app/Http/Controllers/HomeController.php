<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Queries\GetHomePageContent;
use App\Support\Seo\SeoData;
use Illuminate\Contracts\View\View;

final class HomeController extends Controller
{
    public function __invoke(GetHomePageContent $getContent): View
    {
        $content = $getContent();

        return view('home', [
            'content' => $content,
            'seo' => SeoData::forHome($content),
        ]);
    }
}
