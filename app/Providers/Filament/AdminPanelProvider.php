<?php

namespace App\Providers\Filament;

use App\Services\GitHub\Repository;
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
    public function boot() {}

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
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->databaseNotifications()
            ->maxContentWidth(config('speedtest.content_width'))
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
                    ->label(__('translations.settings')),
                NavigationGroup::make()
                    ->label(__('translations.links'))
                    ->collapsible(false),
            ])
            ->navigationItems([
                NavigationItem::make()
                    ->label(fn () => __('translations.documentation'))
                    ->url('https://docs.speedtest-tracker.dev/', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-book-open')
                    ->group(fn () => __('translations.links'))
                    ->sort(97),
                NavigationItem::make()
                    ->label(fn () => __('translations.donate'))
                    ->url('https://github.com/sponsors/alexjustesen', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-banknotes')
                    ->group(fn () => __('translations.links'))
                    ->sort(98),
                NavigationItem::make(config('speedtest.build_version'))
                    ->url('https://github.com/alexjustesen/speedtest-tracker', shouldOpenInNewTab: true)
                    ->icon('tabler-brand-github')
                    ->badge(fn (): string => Repository::updateAvailable() ? __('translations.update_available') : __('translations.up_to_date'))
                    ->group(fn () => __('translations.links'))
                    ->sort(99),
            ]);
    }
}
