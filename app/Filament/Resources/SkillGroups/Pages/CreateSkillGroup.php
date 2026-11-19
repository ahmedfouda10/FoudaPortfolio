<?php

declare(strict_types=1);

namespace App\Filament\Resources\SkillGroups\Pages;

use App\Filament\Resources\SkillGroups\SkillGroupResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateSkillGroup extends CreateRecord
{
    protected static string $resource = SkillGroupResource::class;
}
