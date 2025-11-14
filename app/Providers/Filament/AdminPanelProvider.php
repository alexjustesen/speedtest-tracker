<?php

namespace App\Providers\Filament;

use App\Services\GitHub\Repository;
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
                    ->label(__('general.settings')),
                NavigationGroup::make()
                    ->label(__('general.links'))
                    ->collapsible(false),
            ])
            ->navigationItems([
                NavigationItem::make(__('general.documentation'))
                    ->url('https://docs.speedtest-tracker.dev/', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-book-open')
                    ->group(__('general.links')),
                NavigationItem::make(__('general.donate'))
                    ->url('https://github.com/sponsors/alexjustesen', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-banknotes')
                    ->group(__('general.links')),
                NavigationItem::make(config('speedtest.build_version'))
                    ->url('https://github.com/alexjustesen/speedtest-tracker', shouldOpenInNewTab: true)
                    ->icon('tabler-brand-github')
                    ->badge(fn (): string => Repository::updateAvailable() ? __('general.update_available') : __('general.up_to_date'))
                    ->group(__('general.links')),
            ]);
    }
}
