<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\RecordProjectViewAction;
use App\Queries\GetProjectPageContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class ShowProjectController extends Controller
{
    public function __invoke(
        string $slug,
        Request $request,
        GetProjectPageContent $content,
        RecordProjectViewAction $recordView,
    ): View {
        $page = $content($slug);

        abort_if($page === null, 404);

        $recordView($page->project, $request);

        return view('projects.show', ['page' => $page]);
    }
}
