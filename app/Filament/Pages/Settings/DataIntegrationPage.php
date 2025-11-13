<?php

namespace App\Filament\Pages\Settings;

use App\Jobs\Influxdb\v2\BulkWriteResults;
use App\Jobs\Influxdb\v2\TestConnectionJob;
use App\Settings\DataIntegrationSettings;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;

class DataIntegrationPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?int $navigationSort = 2;

    protected static string $settings = DataIntegrationSettings::class;

    public static function getNavigationGroup(): string
    {
        return __('common.settings');
    }

    public function getTitle(): string
    {
        return __('data_integration.data_integration');
    }

    public static function getNavigationLabel(): string
    {
        return __('data_integration.data_integration');
    }

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::ThreeExtraLarge;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('data_integration.influxdb'))
                    ->description(__('data_integration.influxdb_description'))
                    ->schema([
                        Forms\Components\Toggle::make('influxdb_v2_enabled')
                            ->label(__('data_integration.enable_influxdb'))
                            ->reactive()
                            ->columnSpanFull(),

                        Grid::make(['default' => 1, 'md' => 2])
                            ->hidden(fn (Forms\Get $get) => $get('influxdb_v2_enabled') !== true)
                            ->schema([
                                TextInput::make('influxdb_v2_url')
                                    ->label(__('data_integration.url'))
                                    ->placeholder('http://your-influxdb-instance')
                                    ->maxLength(255)
                                    ->required(fn (Forms\Get $get) => $get('influxdb_v2_enabled') === true)
                                    ->columnSpan(['md' => 1]),
                                TextInput::make('influxdb_v2_org')
                                    ->label(__('data_integration.organization'))
                                    ->maxLength(255)
                                    ->required(fn (Forms\Get $get) => $get('influxdb_v2_enabled') === true)
                                    ->columnSpan(['md' => 1]),
                                TextInput::make('influxdb_v2_bucket')
                                    ->label(__('data_integration.bucket'))
                                    ->placeholder(__('common.speedtest_tracker'))
                                    ->maxLength(255)
                                    ->required(fn (Forms\Get $get) => $get('influxdb_v2_enabled') === true)
                                    ->columnSpan(['md' => 2]),
                                TextInput::make('influxdb_v2_token')
                                    ->label(__('data_integration.token'))
                                    ->maxLength(255)
                                    ->password()
                                    ->required(fn (Forms\Get $get) => $get('influxdb_v2_enabled') === true)
                                    ->autocomplete(false)
                                    ->columnSpan(['md' => 2]),
                                Checkbox::make('influxdb_v2_verify_ssl')
                                    ->label(__('data_integration.verify_ssl'))
                                    ->columnSpanFull(),
                                // Button to send old data to InfluxDB
                                Actions::make([
                                    Action::make('Export current results')
                                        ->label(__('data_integration.export_current_results'))
                                        ->action(function () {
                                            Notification::make()
                                                ->title(__('starting_bulk_data_write_to_influxdb'))
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
                                        ->label(__('data_integration.test_connection'))
                                        ->action(function () {
                                            Notification::make()
                                                ->title(__('sending_test_data_to_influxdb'))
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
                    ->compact()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),
            ])
            ->columns([
                'default' => 1,
            ]);
    }
}
