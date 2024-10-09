<?php

namespace App\Providers\Filament;

use App\Filament\VersionProviders\SpeedtestTrackerVersionProvider;
use Awcodes\FilamentVersions\VersionsPlugin;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
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
            ->colors([
                'primary' => Color::Amber,
            ])
            ->favicon(asset('img/speedtest-tracker-icon.png'))
            ->font(
                'Inter',
                url: asset('fonts/inter/inter.css'),
                provider: LocalFontProvider::class,
            )
            ->sidebarCollapsibleOnDesktop()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([])
            ->plugins([
                VersionsPlugin::make()
                    ->hasDefaults(false)
                    ->items([
                        new SpeedtestTrackerVersionProvider,
                    ]),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->databaseNotifications()
            ->databaseNotificationsPolling(config('speedtest.notification_polling'))
            ->maxContentWidth(config('speedtest.content_width'))
            ->spa()
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Settings'),
                NavigationGroup::make()
                    ->label('Links')
                    ->collapsible(false),
            ])
            ->navigationItems([
                NavigationItem::make('Documentation')
                    ->url('https://docs.speedtest-tracker.dev/', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-book-open')
                    ->group('Links'),
                NavigationItem::make('Donate')
                    ->url('https://github.com/sponsors/alexjustesen', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-banknotes')
                    ->group('Links'),
                NavigationItem::make('GitHub')
                    ->url('https://github.com/alexjustesen/speedtest-tracker', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-code-bracket')
                    ->group('Links'),
            ]);
    }
}
