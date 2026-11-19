<?php

declare(strict_types=1);

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\ProjectLinkType;
use App\Enums\ProjectStatus;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

final class ProjectForm
{
    /**
     * Max upload size for project images, in kilobytes.
     */
    public const int MAX_IMAGE_SIZE = 4096;

    /**
     * @var list<string>
     */
    private const array INTEGRATION_SUGGESTIONS = ['Shopify', 'Salla', 'Zid', 'Magento', 'ERPNext', 'OpenAI API'];

    /**
     * @var list<string>
     */
    private const array TECH_SUGGESTIONS = ['Laravel', 'PHP', 'MySQL', 'REST APIs', 'GraphQL', 'Webhooks', 'Queues', 'Redis', 'Livewire', 'Filament', 'Tailwind CSS'];

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('project')
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->tabs([
                    self::overviewTab(),
                    self::caseStudyTab(),
                    self::mediaTab(),
                    self::linksTab(),
                    self::seoTab(),
                ]),
        ]);
    }

    private static function overviewTab(): Tab
    {
        return Tab::make(__('admin.tabs.overview'))
            ->columns(2)
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state, string $operation): void {
                        if ($operation === 'create') {
                            $set('slug', Str::slug((string) $state));
                        }
                    }),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->alphaDash()
                    ->unique(ignoreRecord: true)
                    ->helperText(__('admin.help.slug')),
                TextInput::make('subtitle')->maxLength(255),
                TextInput::make('tagline')->maxLength(255),
                Textarea::make('summary')->rows(3)->maxLength(2000)->columnSpanFull(),
                TextInput::make('company')->maxLength(255),
                TextInput::make('year')->integer()->minValue(2000)->maxValue(2100),
                Select::make('status')->options(ProjectStatus::class),
                Grid::make(2)->schema([
                    Toggle::make('is_featured'),
                    Toggle::make('is_published')->helperText(__('admin.help.is_published')),
                ]),
            ]);
    }

    private static function caseStudyTab(): Tab
    {
        $toolbar = [
            ['bold', 'italic', 'link'],
            ['h2', 'h3'],
            ['bulletList', 'orderedList', 'blockquote', 'codeBlock'],
            ['undo', 'redo'],
        ];

        return Tab::make(__('admin.tabs.case_study'))
            ->schema([
                Textarea::make('problem')->rows(3)->maxLength(5000),
                Textarea::make('my_role')->rows(3)->maxLength(5000),
                RichEditor::make('solution')->toolbarButtons($toolbar),
                RichEditor::make('results')->toolbarButtons($toolbar),
                TagsInput::make('integrations')->suggestions(self::INTEGRATION_SUGGESTIONS)->reorderable(),
                TagsInput::make('tech_stack')->suggestions(self::TECH_SUGGESTIONS)->reorderable(),
            ]);
    }

    private static function mediaTab(): Tab
    {
        return Tab::make(__('admin.tabs.media'))
            ->schema([
                FileUpload::make('cover_image')
                    ->image()
                    ->disk('public')
                    ->directory('projects/covers')
                    ->visibility('public')
                    ->imageEditor()
                    ->imageEditorAspectRatioOptions(['16:9', '4:3', null])
                    ->maxSize(self::MAX_IMAGE_SIZE)
                    ->helperText(__('admin.help.cover_image')),
                TextInput::make('cover_image_alt')
                    ->maxLength(255)
                    ->requiredWith('cover_image')
                    ->helperText(__('admin.help.alt_text')),
                Repeater::make('images')
                    ->relationship()
                    ->orderColumn('sort_order')
                    ->reorderable()
                    ->collapsible()
                    ->defaultItems(0)
                    ->columns(2)
                    ->addActionLabel(__('admin.actions.add_gallery_image'))
                    ->schema([
                        FileUpload::make('path')
                            ->image()
                            ->required()
                            ->disk('public')
                            ->directory('projects/gallery')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(self::MAX_IMAGE_SIZE)
                            ->columnSpanFull(),
                        TextInput::make('alt')->required()->maxLength(255),
                        TextInput::make('caption')->maxLength(255),
                    ]),
            ]);
    }

    private static function linksTab(): Tab
    {
        return Tab::make(__('admin.tabs.links'))
            ->schema([
                Repeater::make('links')
                    ->columns(3)
                    ->reorderable()
                    ->defaultItems(0)
                    ->addActionLabel(__('admin.actions.add_link'))
                    ->schema([
                        TextInput::make('label')->required()->maxLength(100),
                        TextInput::make('url')->required()->url()->maxLength(2048),
                        Select::make('type')->options(ProjectLinkType::class)->required()->default(ProjectLinkType::Website->value),
                    ]),
            ]);
    }

    private static function seoTab(): Tab
    {
        return Tab::make(__('admin.tabs.seo'))
            ->schema([
                TextInput::make('meta_title')->maxLength(70)->helperText(__('admin.help.meta_title')),
                Textarea::make('meta_description')->rows(2)->maxLength(160)->helperText(__('admin.help.meta_description')),
            ]);
    }
}
