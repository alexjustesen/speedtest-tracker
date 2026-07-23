<?php

namespace App\Filament\Pages\Settings;

use App\Settings\GeneralSettings;
use Filament\Forms\Components\Select;
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
                                        Select::make('default_chart_range')
                                            ->label(__('settings/general.default_chart_range'))
                                            ->helperText(__('settings/general.default_chart_range_helper_text'))
                                            ->native(false)
                                            ->options([
                                                '24h' => __('settings/general.default_chart_range_24h'),
                                                'week' => __('settings/general.default_chart_range_week'),
                                                'month' => __('settings/general.default_chart_range_month'),
                                            ])
                                            ->required(),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
