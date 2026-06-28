<?php

namespace App\Filament\Pages\Settings;

use App\Settings\GeneralSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                        Tab::make(__('settings/general.display'))
                            ->icon(Heroicon::OutlinedComputerDesktop)
                            ->schema([
                                Grid::make(['default' => 1, 'md' => 2])
                                    ->schema([
                                        Select::make('display_timezone')
                                            ->label(__('settings/general.display_timezone'))
                                            ->helperText(__('settings/general.display_timezone_helper_text'))
                                            ->options(fn (): array => array_combine(
                                                \DateTimeZone::listIdentifiers(),
                                                \DateTimeZone::listIdentifiers(),
                                            ))
                                            ->searchable()
                                            ->required(),
                                        TextInput::make('datetime_format')
                                            ->label(__('settings/general.datetime_format'))
                                            ->helperText(__('settings/general.datetime_format_helper_text'))
                                            ->hintAction(
                                                Action::make('datetime_format_docs')
                                                    ->label(__('settings/general.datetime_format_docs'))
                                                    ->icon(Heroicon::OutlinedQuestionMarkCircle)
                                                    ->url('https://www.php.net/manual/en/datetime.format.php')
                                                    ->openUrlInNewTab()
                                            )
                                            ->required(),
                                        TextInput::make('chart_datetime_format')
                                            ->label(__('settings/general.chart_datetime_format'))
                                            ->helperText(__('settings/general.chart_datetime_format_helper_text'))
                                            ->hintAction(
                                                Action::make('chart_datetime_format_docs')
                                                    ->label(__('settings/general.datetime_format_docs'))
                                                    ->icon(Heroicon::OutlinedQuestionMarkCircle)
                                                    ->url('https://www.php.net/manual/en/datetime.format.php')
                                                    ->openUrlInNewTab()
                                            )
                                            ->required(),
                                        Toggle::make('chart_begin_at_zero')
                                            ->label(__('settings/general.chart_begin_at_zero'))
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpanFull(),

                        Tab::make(__('settings/general.charts'))
                            ->icon(Heroicon::OutlinedChartBar)
                            ->schema([
                                Grid::make(['default' => 1, 'md' => 2])
                                    ->schema([
                                        Select::make('default_chart_range')
                                            ->label(__('settings/general.default_chart_range'))
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

                        Tab::make(__('settings/general.data_retention'))
                            ->icon(Heroicon::OutlinedTrash)
                            ->schema([
                                Grid::make(['default' => 1, 'md' => 2])
                                    ->schema([
                                        TextInput::make('prune_results_older_than')
                                            ->label(__('settings/general.prune_results_older_than'))
                                            ->hint(__('settings/general.prune_results_older_than_hint'))
                                            ->helperText(__('settings/general.prune_results_older_than_helper_text'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->required(),
                                    ]),
                            ])
                            ->columnSpanFull(),

                        Tab::make(__('settings/general.connectivity'))
                            ->icon(Heroicon::OutlinedWifi)
                            ->schema([
                                Grid::make(['default' => 1, 'md' => 2])
                                    ->schema([
                                        TextInput::make('speedtest_external_ip_url')
                                            ->label(__('settings/general.speedtest_external_ip_url'))
                                            ->helperText(__('settings/general.speedtest_external_ip_url_helper_text'))
                                            ->url()
                                            ->required(),
                                        TextInput::make('speedtest_internet_check_hostname')
                                            ->label(__('settings/general.speedtest_internet_check_hostname'))
                                            ->helperText(__('settings/general.speedtest_internet_check_hostname_helper_text'))
                                            ->required(),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
