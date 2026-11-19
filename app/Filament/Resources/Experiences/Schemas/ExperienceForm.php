<?php

declare(strict_types=1);

namespace App\Filament\Resources\Experiences\Schemas;

use App\Enums\EmploymentType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class ExperienceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('admin.sections.position'))
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    TextInput::make('role')->required()->maxLength(255),
                    TextInput::make('company')->required()->maxLength(255),
                    TextInput::make('location')->maxLength(255),
                    Select::make('employment_type')->options(EmploymentType::class),
                    DatePicker::make('start_date')
                        ->required()
                        ->native(false)
                        ->displayFormat('M Y')
                        ->maxDate(now()),
                    DatePicker::make('end_date')
                        ->native(false)
                        ->displayFormat('M Y')
                        ->afterOrEqual('start_date')
                        ->helperText(__('admin.help.end_date_present')),
                ]),
            Section::make(__('admin.sections.details'))
                ->columnSpanFull()
                ->schema([
                    Textarea::make('description')->rows(3)->maxLength(2000),
                    Repeater::make('bullet_points')
                        ->simple(Textarea::make('point')->required()->rows(2)->maxLength(500))
                        ->reorderable()
                        ->defaultItems(0)
                        ->addActionLabel(__('admin.actions.add_bullet_point')),
                    Toggle::make('is_visible')->default(true),
                ]),
        ]);
    }
}
