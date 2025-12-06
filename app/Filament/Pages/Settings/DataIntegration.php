<?php

namespace App\Filament\Pages\Settings;

use App\Jobs\Influxdb\v2\BulkWriteResults;
use App\Jobs\Influxdb\v2\TestConnectionJob;
use App\Settings\DataIntegrationSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class DataIntegration extends SettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'tabler-database';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    public function getTitle(): string
    {
        return __('settings/data_integration.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('settings/data_integration.label');
    }

    protected static string $settings = DataIntegrationSettings::class;

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
                        Tab::make(__('settings/data_integration.influxdb_v2'))
                            ->icon(Heroicon::OutlinedCircleStack)
                            ->schema([
                                Toggle::make('influxdb_v2_enabled')
                                    ->label(__('settings/data_integration.influxdb_v2_enabled'))
                                    ->helperText(__('settings/data_integration.influxdb_v2_description'))
                                    ->reactive()
                                    ->columnSpanFull(),
                                Grid::make(['default' => 1, 'md' => 3])
                                    ->hidden(fn (Get $get) => $get('influxdb_v2_enabled') !== true)
                                    ->schema([
                                        TextInput::make('influxdb_v2_url')
                                            ->label(__('settings/data_integration.influxdb_v2_url'))
                                            ->placeholder(__('settings/data_integration.influxdb_v2_url_placeholder'))
                                            ->maxLength(255)
                                            ->required(fn (Get $get) => $get('influxdb_v2_enabled') === true)
                                            ->columnSpan(['md' => 1]),
                                        TextInput::make('influxdb_v2_org')
                                            ->label(__('settings/data_integration.influxdb_v2_org'))
                                            ->maxLength(255)
                                            ->required(fn (Get $get) => $get('influxdb_v2_enabled') === true)
                                            ->columnSpan(['md' => 1]),
                                        TextInput::make('influxdb_v2_bucket')
                                            ->placeholder(__('settings/data_integration.influxdb_v2_bucket_placeholder'))
                                            ->label(__('settings/data_integration.influxdb_v2_bucket'))
                                            ->maxLength(255)
                                            ->required(fn (Get $get) => $get('influxdb_v2_enabled') === true)
                                            ->columnSpan(['md' => 2]),
                                        TextInput::make('influxdb_v2_token')
                                            ->label(__('settings/data_integration.influxdb_v2_token'))
                                            ->maxLength(255)
                                            ->password()
                                            ->required(fn (Get $get) => $get('influxdb_v2_enabled') === true)
                                            ->columnSpan(['md' => 2]),
                                        Checkbox::make('influxdb_v2_verify_ssl')
                                            ->label(__('settings/data_integration.influxdb_v2_verify_ssl'))
                                            ->columnSpanFull(),
                                        // Button to send old data to InfluxDB
                                        Actions::make([
                                            Action::make('Export current results')
                                                ->label(__('general.export_current_results'))
                                                ->action(function () {
                                                    Notification::make()
                                                        ->title(__('settings/data_integration.starting_bulk_data_write_to_influxdb'))
                                                        ->info()
                                                        ->send();

                                                    BulkWriteResults::dispatch(Auth::user());
                                                })
                                                ->color('primary')
                                                ->icon('heroicon-o-cloud-arrow-up')
                                                ->visible(fn (): bool => app(DataIntegrationSettings::class)->influxdb_v2_enabled),
                                        ]),
                                        // Button to test InfluxDB connection
                                        Actions::make([
                                            Action::make('Test connection')
                                                ->label(__('settings/data_integration.test_connection'))
                                                ->action(function () {
                                                    Notification::make()
                                                        ->title(__('settings/data_integration.sending_test_data_to_influxdb'))
                                                        ->info()
                                                        ->send();

                                                    TestConnectionJob::dispatch(Auth::user());
                                                })
                                                ->color('primary')
                                                ->icon('heroicon-o-check-circle')
                                                ->visible(fn (): bool => app(DataIntegrationSettings::class)->influxdb_v2_enabled),
                                        ]),
                                    ]),
                            ])
                            ->columnSpanFull(),
                        Tab::make(__('settings/data_integration.prometheus'))
                            ->icon(Heroicon::OutlinedChartBar)
                            ->schema([
                                Toggle::make('prometheus_enabled')
                                    ->label(__('settings/data_integration.prometheus_enabled'))
                                    ->helperText(__('settings/data_integration.prometheus_enabled_helper_text'))
                                    ->reactive()
                                    ->columnSpanFull(),
                                Grid::make(['default' => 1, 'md' => 3])
                                    ->hidden(fn (Get $get) => $get('prometheus_enabled') !== true)
                                    ->schema([
                                        TagsInput::make('prometheus_allowed_ips')
                                            ->label(__('settings/data_integration.prometheus_allowed_ips'))
                                            ->helperText(__('settings/data_integration.prometheus_allowed_ips_helper'))
                                            ->placeholder('192.168.1.100')
                                            ->splitKeys(['Tab', ',', ' '])
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
