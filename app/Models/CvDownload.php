<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CvDownloadFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * One tracked CV download. Append-only, so there is no `updated_at`.
 */
#[Fillable(['ip_hash', 'user_agent', 'referer'])]
class CvDownload extends Model
{
    /** @use HasFactory<CvDownloadFactory> */
    use HasFactory;

    public const ?string UPDATED_AT = null;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'immutable_datetime',
        ];
    }
}
