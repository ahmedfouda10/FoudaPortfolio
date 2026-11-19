<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Edits the single SiteSetting row: identity, hero stats, About section,
 * contact links, CV and SEO defaults.
 *
 * @property-read Schema $form
 */
final class ManageSiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'site-settings';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    private ?SiteSetting $settings = null;

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.pages.site_settings.title');
    }

    public function getTitle(): string
    {
        return __('admin.pages.site_settings.title');
    }

    public function mount(): void
    {
        $this->form->fill($this->settings()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model($this->settings())
            ->operation('edit')
            ->statePath('data')
            ->components([
                Tabs::make('settings')
                    ->persistTabInQueryString()
                    ->tabs([
                        $this->profileTab(),
                        $this->aboutTab(),
                        $this->contactTab(),
                        $this->seoTab(),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')
                            ->label(__('admin.actions.save'))
                            ->submit('save')
                            ->keyBindings(['mod+s']),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        $this->settings()->update($this->form->getState());

        Notification::make()
            ->success()
            ->title(__('admin.notifications.settings_saved'))
            ->send();
    }

    private function settings(): SiteSetting
    {
        return $this->settings ??= SiteSetting::current();
    }

    private function profileTab(): Tab
    {
        return Tab::make(__('admin.tabs.profile'))
            ->columns(2)
            ->schema([
                TextInput::make('full_name')->required()->maxLength(255),
                TextInput::make('headline')->maxLength(255),
                Textarea::make('short_bio')->rows(2)->maxLength(500)->columnSpanFull()->helperText(__('admin.help.short_bio')),
                TextInput::make('location')->maxLength(255),
                TextInput::make('availability_status')->maxLength(255),
                TextInput::make('years_of_experience')->integer()->minValue(0)->maxValue(60)->helperText(__('admin.help.hero_stat')),
                TextInput::make('projects_shipped')->integer()->minValue(0)->maxValue(10000)->helperText(__('admin.help.hero_stat')),
                TextInput::make('platforms_integrated')->integer()->minValue(0)->maxValue(1000)->helperText(__('admin.help.hero_stat')),
                FileUpload::make('avatar')
                    ->image()
                    ->disk('public')
                    ->directory('profile')
                    ->visibility('public')
                    ->imageEditor()
                    ->imageEditorAspectRatioOptions(['1:1'])
                    ->maxSize(2048)
                    ->columnSpanFull(),
                FileUpload::make('cv_file')
                    ->disk('local')
                    ->directory('cv')
                    ->visibility('private')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(5120)
                    ->downloadable()
                    ->helperText(__('admin.help.cv_file'))
                    ->columnSpanFull(),
            ]);
    }

    private function aboutTab(): Tab
    {
        return Tab::make(__('admin.tabs.about'))
            ->schema([
                RichEditor::make('about_body')->toolbarButtons([
                    ['bold', 'italic', 'link'],
                    ['bulletList', 'orderedList'],
                    ['undo', 'redo'],
                ]),
                Repeater::make('about_highlights')
                    ->simple(TextInput::make('highlight')->required()->maxLength(255))
                    ->reorderable()
                    ->defaultItems(0)
                    ->addActionLabel(__('admin.actions.add_highlight')),
            ]);
    }

    private function contactTab(): Tab
    {
        return Tab::make(__('admin.tabs.contact'))
            ->columns(2)
            ->schema([
                TextInput::make('email')->email()->maxLength(255)->helperText(__('admin.help.contact_email')),
                TextInput::make('phone')->tel()->maxLength(50),
                TextInput::make('whatsapp')->tel()->maxLength(50)->helperText(__('admin.help.whatsapp')),
                TextInput::make('linkedin_url')->url()->maxLength(255),
                TextInput::make('github_url')->url()->maxLength(255),
                TextInput::make('freelancer_url')->url()->maxLength(255),
                TextInput::make('source_code_url')->url()->maxLength(255)->helperText(__('admin.help.source_code_url')),
            ]);
    }

    private function seoTab(): Tab
    {
        return Tab::make(__('admin.tabs.seo'))
            ->schema([
                TextInput::make('meta_title')->maxLength(70)->helperText(__('admin.help.meta_title')),
                Textarea::make('meta_description')->rows(2)->maxLength(160)->helperText(__('admin.help.meta_description')),
                FileUpload::make('og_image')
                    ->image()
                    ->disk('public')
                    ->directory('seo')
                    ->visibility('public')
                    ->imageEditor()
                    ->imageEditorAspectRatioOptions(['1.91:1'])
                    ->maxSize(2048)
                    ->helperText(__('admin.help.og_image')),
            ]);
    }
}
