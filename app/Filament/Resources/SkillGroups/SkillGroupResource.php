<?php

declare(strict_types=1);

namespace App\Filament\Resources\SkillGroups;

use App\Filament\Resources\SkillGroups\Pages\CreateSkillGroup;
use App\Filament\Resources\SkillGroups\Pages\EditSkillGroup;
use App\Filament\Resources\SkillGroups\Pages\ListSkillGroups;
use App\Filament\Resources\SkillGroups\RelationManagers\SkillsRelationManager;
use App\Filament\Resources\SkillGroups\Schemas\SkillGroupForm;
use App\Filament\Resources\SkillGroups\Tables\SkillGroupsTable;
use App\Models\SkillGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

final class SkillGroupResource extends Resource
{
    protected static ?string $model = SkillGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?int $navigationSort = 20;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.content');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.skill_group.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.skill_group.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return SkillGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SkillGroupsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SkillsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSkillGroups::route('/'),
            'create' => CreateSkillGroup::route('/create'),
            'edit' => EditSkillGroup::route('/{record}/edit'),
        ];
    }
}
