<?php

namespace App\Filament\Pages\Settings;

use App\Settings\GeneralSettings;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

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
                                        TextInput::make('chart_default_range')
                                            ->label(__('settings/general.chart_default_range'))
                                            ->helperText(__('settings/general.chart_default_range_helper_text'))
                                            ->numeric()
                                            ->minValue(1)
                                            ->required(),
                                        TextInput::make('chart_datetime_format')
                                            ->label(__('settings/general.chart_datetime_format'))
                                            ->helperText(__('settings/general.chart_datetime_format_helper_text'))
                                            ->hint(new HtmlString('&#x1f517;<a href="https://www.php.net/manual/en/datetime.format.php" target="_blank" rel="nofollow">'.__('settings/general.datetime_format_docs').'</a>'))
                                            ->required(),
                                        Checkbox::make('chart_begin_at_zero')
                                            ->label(__('settings/general.chart_begin_at_zero'))
                                            ->helperText(__('settings/general.chart_begin_at_zero_helper_text')),
                                        Checkbox::make('chart_only_show_avg_latency')
                                            ->label(__('settings/general.chart_only_show_avg_latency'))
                                            ->helperText(__('settings/general.chart_only_show_avg_latency_helper_text')),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
