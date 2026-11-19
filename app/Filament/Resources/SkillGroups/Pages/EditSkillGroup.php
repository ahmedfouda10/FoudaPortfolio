<?php

declare(strict_types=1);

namespace App\Filament\Resources\SkillGroups\Pages;

use App\Filament\Resources\SkillGroups\SkillGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditSkillGroup extends EditRecord
{
    protected static string $resource = SkillGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
