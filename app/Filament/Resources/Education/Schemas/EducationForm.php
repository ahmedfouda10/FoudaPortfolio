<?php

declare(strict_types=1);

namespace App\Filament\Resources\Education\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class EducationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    TextInput::make('degree')->required()->maxLength(255),
                    TextInput::make('field')->maxLength(255),
                    TextInput::make('institution')->required()->maxLength(255)->columnSpanFull(),
                    TextInput::make('start_year')->integer()->minValue(1950)->maxValue(2100),
                    TextInput::make('end_year')->integer()->minValue(1950)->maxValue(2100)->gte('start_year'),
                    Textarea::make('description')->rows(3)->maxLength(2000)->columnSpanFull(),
                    Toggle::make('is_visible')->default(true),
                ]),
        ]);
    }
}
