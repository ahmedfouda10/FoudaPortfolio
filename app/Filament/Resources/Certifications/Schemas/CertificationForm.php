<?php

declare(strict_types=1);

namespace App\Filament\Resources\Certifications\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

final class CertificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    TextInput::make('title')->required()->maxLength(255),
                    TextInput::make('issuer')->maxLength(255),
                    Toggle::make('is_in_progress')->live()->columnSpanFull(),
                    DatePicker::make('started_on')->native(false)->displayFormat('M Y'),
                    DatePicker::make('completed_on')
                        ->native(false)
                        ->displayFormat('M Y')
                        ->afterOrEqual('started_on')
                        ->hidden(fn (Get $get): bool => (bool) $get('is_in_progress')),
                    Repeater::make('credential_urls')
                        ->simple(TextInput::make('url')->url()->required()->maxLength(2048))
                        ->defaultItems(0)
                        ->addActionLabel(__('admin.actions.add_credential_link'))
                        ->columnSpanFull(),
                    Textarea::make('description')->rows(3)->maxLength(2000)->columnSpanFull(),
                    Toggle::make('is_visible')->default(true),
                ]),
        ]);
    }
}
