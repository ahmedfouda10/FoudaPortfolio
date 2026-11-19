<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\RecordCvDownloadAction;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The CV lives on the private disk, so this tracked route is the only way to get it.
 */
final class DownloadCvController extends Controller
{
    public function __invoke(Request $request, RecordCvDownloadAction $recordDownload): StreamedResponse
    {
        $settings = SiteSetting::current();
        $disk = Storage::disk('local');

        abort_if($settings->cv_file === null || ! $disk->exists($settings->cv_file), 404);

        $recordDownload($request);

        return $disk->download($settings->cv_file, Str::slug($settings->full_name).'-cv.pdf', [
            'Cache-Control' => 'no-store, private',
            'X-Robots-Tag' => 'noindex',
        ]);
    }
}
