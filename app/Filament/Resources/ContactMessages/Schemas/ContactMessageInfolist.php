<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactMessages\Schemas;

use App\Models\ContactMessage;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class ContactMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('admin.sections.sender'))
                ->columns(3)
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('email')->copyable(),
                    TextEntry::make('created_at')->dateTime(),
                    TextEntry::make('inquiry_type')->badge(),
                    TextEntry::make('budget')
                        ->visible(fn (ContactMessage $record): bool => filled($record->budget)),
                    TextEntry::make('status')->badge(),
                ]),
            Section::make(__('admin.sections.message'))
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('subject')->weight('bold'),
                    TextEntry::make('message')->hiddenLabel()->prose()->extraAttributes(['class' => 'whitespace-pre-line']),
                ]),
            Section::make(__('admin.sections.metadata'))
                ->columnSpanFull()
                ->collapsed()
                ->schema([
                    TextEntry::make('user_agent')->placeholder('—'),
                    TextEntry::make('ip_hash')->placeholder('—')->fontFamily('mono'),
                ]),
        ]);
    }
}
