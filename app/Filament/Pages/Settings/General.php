<?php

namespace App\Filament\Pages\Settings;

use App\Settings\GeneralSettings;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class General extends SettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'tabler-settings';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    public function getTitle(): string
    {
        return __('settings/general.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('settings/general.label');
    }

    protected static string $settings = GeneralSettings::class;

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->schema([
                        Tab::make(__('settings/general.charts'))
                            ->icon(Heroicon::OutlinedChartBar)
                            ->schema([
                                Grid::make(['default' => 1, 'md' => 2])
                                    ->schema([
                                        TextInput::make('default_chart_range')
                                            ->label(__('settings/general.default_chart_range'))
                                            ->helperText(__('settings/general.default_chart_range_helper_text'))
                                            ->numeric()
                                            ->minValue(1)
                                            ->required(),
                                    ]),
                            ])
                            ->columnSpanFull(),

                        Tab::make(__('settings/general.connectivity'))
                            ->icon(Heroicon::OutlinedGlobeAlt)
                            ->schema([
                                Grid::make(['default' => 1, 'md' => 2])
                                    ->schema([
                                        TextInput::make('external_ip_url')
                                            ->label(__('settings/general.external_ip_url'))
                                            ->helperText(__('settings/general.external_ip_url_helper_text'))
                                            ->url()
                                            ->required(),
                                        TextInput::make('internet_check_hostname')
                                            ->label(__('settings/general.internet_check_hostname'))
                                            ->helperText(__('settings/general.internet_check_hostname_helper_text'))
                                            ->required(),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
