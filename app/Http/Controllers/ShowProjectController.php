<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Queries\GetProjectPageContent;
use Illuminate\Contracts\View\View;

final class ShowProjectController extends Controller
{
    public function __invoke(string $slug, GetProjectPageContent $content): View
    {
        $page = $content($slug);

        abort_if($page === null, 404);

        return view('projects.show', ['page' => $page]);
    }
}
