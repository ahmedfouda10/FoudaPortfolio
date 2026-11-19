<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\CvDownload;
use App\Support\IpHasher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final readonly class RecordCvDownloadAction
{
    public function __construct(private IpHasher $ipHasher) {}

    public function __invoke(Request $request): CvDownload
    {
        return CvDownload::query()->create([
            'ip_hash' => $this->ipHasher->hash($request->ip()),
            'user_agent' => Str::limit((string) $request->userAgent(), 509),
            'referer' => Str::limit((string) $request->headers->get('referer'), 2045) ?: null,
        ]);
    }
}
