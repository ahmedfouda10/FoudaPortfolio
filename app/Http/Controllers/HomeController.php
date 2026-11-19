<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Queries\GetHomePageContent;
use Illuminate\Contracts\View\View;

final class HomeController extends Controller
{
    public function __invoke(GetHomePageContent $content): View
    {
        return view('home', ['content' => $content()]);
    }
}
