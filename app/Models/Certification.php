<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use App\Models\Concerns\HasVisibility;
use Database\Factories\CertificationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A certificate or training programme. `credential_urls` is a list of URLs.
 */
#[Fillable([
    'title', 'issuer', 'started_on', 'completed_on', 'is_in_progress',
    'credential_urls', 'description', 'sort_order', 'is_visible',
])]
class Certification extends Model
{
    /** @use HasFactory<CertificationFactory> */
    use HasFactory;

    use HasSortOrder;
    use HasVisibility;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_on' => 'immutable_date',
            'completed_on' => 'immutable_date',
            'is_in_progress' => 'boolean',
            'credential_urls' => 'array',
            'sort_order' => 'integer',
            'is_visible' => 'boolean',
        ];
    }
}
