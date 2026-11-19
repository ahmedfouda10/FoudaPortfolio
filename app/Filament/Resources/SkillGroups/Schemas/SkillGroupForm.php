<?php

declare(strict_types=1);

namespace App\Filament\Resources\SkillGroups\Schemas;

use App\Filament\Support\HeroiconOptions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class SkillGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    TextInput::make('name')->required()->maxLength(255)->unique(ignoreRecord: true),
                    Select::make('icon')->options(HeroiconOptions::outline())->searchable(),
                    Toggle::make('is_visible')->default(true),
                ]),
        ]);
    }
}
