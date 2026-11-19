<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\AvatarProviders\InitialsAvatarProvider;
use App\Http\Middleware\AddSecurityHeaders;
use App\Http\Middleware\PreventIndexingAndCaching;
use App\Support\PublicContentCache;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Enums\ThemeMode;
use Filament\Forms\Components\Field;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Infolists\Components\Entry;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Lang;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile(isSimple: false)
            ->multiFactorAuthentication(
                AppAuthentication::make()->recoverable(),
                isRequired: fn (): bool => (bool) config('portfolio.admin.require_mfa'),
            )
            ->brandName(fn (): string => __('admin.brand'))
            ->colors([
                'primary' => Color::Indigo,
                'gray' => Color::Zinc,
            ])
            ->defaultThemeMode(ThemeMode::Dark)
            ->defaultAvatarProvider(InitialsAvatarProvider::class)
            ->sidebarCollapsibleOnDesktop()
            ->unsavedChangesAlerts()
            ->navigationGroups([
                NavigationGroup::make(fn (): string => __('admin.navigation.inbox')),
                NavigationGroup::make(fn (): string => __('admin.navigation.content')),
                NavigationGroup::make(fn (): string => __('admin.navigation.settings')),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_START,
                fn (): string => '<meta name="robots" content="noindex, nofollow">',
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->middleware([
                AddSecurityHeaders::class,
                PreventIndexingAndCaching::class,
            ], isPersistent: true)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public function boot(): void
    {
        $this->translateLabelsFromLangFiles();
        $this->flushPublicCacheAfterReordering();
    }

    /**
     * Drag-and-drop reordering updates rows with a single query (no model
     * events), so the public pages are refreshed explicitly afterwards.
     */
    private function flushPublicCacheAfterReordering(): void
    {
        Table::configureUsing(function (Table $table): void {
            $table->afterReordering(fn () => app(PublicContentCache::class)->flush());
        });
    }

    /**
     * Every field, column and entry takes its label from `lang/{locale}/admin.php`
     * (`admin.fields.<name>`) when a translation exists, so the dashboard can be
     * translated later without touching the resource classes.
     */
    private function translateLabelsFromLangFiles(): void
    {
        $translate = static function (string $name): ?string {
            $key = "admin.fields.{$name}";

            return Lang::has($key) ? (string) __($key) : null;
        };

        Field::configureUsing(function (Field $field) use ($translate): void {
            if (($label = $translate($field->getName())) !== null) {
                $field->label($label);
            }
        });

        Column::configureUsing(function (Column $column) use ($translate): void {
            if (($label = $translate($column->getName())) !== null) {
                $column->label($label);
            }
        });

        Entry::configureUsing(function (Entry $entry) use ($translate): void {
            if (($label = $translate($entry->getName())) !== null) {
                $entry->label($label);
            }
        });
    }
}
