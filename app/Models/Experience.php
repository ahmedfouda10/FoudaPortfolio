<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EmploymentType;
use App\Models\Concerns\HasSortOrder;
use App\Models\Concerns\HasVisibility;
use Database\Factories\ExperienceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'company', 'role', 'location', 'employment_type', 'start_date', 'end_date',
    'description', 'bullet_points', 'sort_order', 'is_visible',
])]
class Experience extends Model
{
    /** @use HasFactory<ExperienceFactory> */
    use HasFactory;

    use HasSortOrder;
    use HasVisibility;

    /**
     * A position without an end date is the current one ("Present").
     */
    public function isCurrent(): bool
    {
        return $this->end_date === null;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'employment_type' => EmploymentType::class,
            'start_date' => 'immutable_date',
            'end_date' => 'immutable_date',
            'bullet_points' => 'array',
            'sort_order' => 'integer',
            'is_visible' => 'boolean',
        ];
    }
}
