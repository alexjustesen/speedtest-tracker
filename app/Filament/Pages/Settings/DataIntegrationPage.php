<?php

namespace App\Filament\Pages\Settings;

use App\Jobs\Influxdb\v2\BulkWriteResults;
use App\Jobs\Influxdb\v2\TestConnectionJob;
use App\Settings\DataIntegrationSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class DataIntegrationPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Data Integration';

    protected static ?string $navigationLabel = 'Data Integration';

    protected static string $settings = DataIntegrationSettings::class;

    public static function canAccess(): bool
    {
        return auth()->user()->is_admin;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->is_admin;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Integration Settings')
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Prometheus')
                            ->schema([
                                Forms\Components\Toggle::make('prometheus_enabled')
                                    ->label('Enable Prometheus')
                                    ->hint(new HtmlString('<a href="https://docs.speedtest-tracker.dev/settings/data-platforms/prometheus" target="_blank">Prometheus Documentation</a>'))
                                    ->helpertext('When enabled there will be a /metrics endpoint for prometheus to scrape the last test result.'),
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('Open Metrics Page')
                                        ->hidden(fn (Forms\Get $get) => $get('prometheus_enabled') !== true)
                                        ->label('View Metrics')
                                        ->action(function () {
                                            return redirect(config('app.url').'/metrics');
                                        })
                                        ->color('primary')
                                        ->icon('heroicon-o-eye'),
                                ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('InfluxDB v2')
                            ->schema([
                                Forms\Components\Toggle::make('influxdb_v2_enabled')
                                    ->hint(new HtmlString('<a href="https://docs.speedtest-tracker.dev/settings/data-platforms/influxdb2" target="_blank">InfluxDB Documentation</a>'))
                                    ->helpertext('When enabled all resutls will be sent to InfluxDB.')
                                    ->label('Enable InfluxDB v2')
                                    ->reactive(),
                                Forms\Components\Grid::make(['default' => 1, 'md' => 3])
                                    ->hidden(fn (Forms\Get $get) => $get('influxdb_v2_enabled') !== true)
                                    ->schema([
                                        Forms\Components\TextInput::make('influxdb_v2_url')
                                            ->label('URL')
                                            ->placeholder('http://your-influxdb-instance')
                                            ->maxLength(255)
                                            ->required(fn (Forms\Get $get) => $get('influxdb_v2_enabled') === true)
                                            ->columnSpan(['md' => 1]),
                                        Forms\Components\TextInput::make('influxdb_v2_org')
                                            ->label('Org')
                                            ->maxLength(255)
                                            ->required(fn (Forms\Get $get) => $get('influxdb_v2_enabled') === true)
                                            ->columnSpan(['md' => 1]),
                                        Forms\Components\TextInput::make('influxdb_v2_bucket')
                                            ->placeholder('speedtest-tracker')
                                            ->label('Bucket')
                                            ->maxLength(255)
                                            ->required(fn (Forms\Get $get) => $get('influxdb_v2_enabled') === true)
                                            ->columnSpan(['md' => 2]),
                                        Forms\Components\TextInput::make('influxdb_v2_token')
                                            ->label('Token')
                                            ->maxLength(255)
                                            ->password()
                                            ->required(fn (Forms\Get $get) => $get('influxdb_v2_enabled') === true)
                                            ->disableAutocomplete()
                                            ->columnSpan(['md' => 2]),
                                        Forms\Components\Checkbox::make('influxdb_v2_verify_ssl')
                                            ->label('Verify SSL')
                                            ->columnSpanFull(),
                                        // Button to send old data to InfluxDB
                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('Export current results')
                                                ->label('Export current results')
                                                ->action(function () {
                                                    Notification::make()
                                                        ->title('Starting bulk data write to Influxdb')
                                                        ->info()
                                                        ->send();

                                                    BulkWriteResults::dispatch(Auth::user());
                                                })
                                                ->color('primary')
                                                ->icon('heroicon-o-cloud-arrow-up')
                                                ->visible(fn (): bool => app(DataIntegrationSettings::class)->influxdb_v2_enabled),
                                        ]),
                                        // Button to test InfluxDB connection
                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('Test connection')
                                                ->label('Test connection')
                                                ->action(function () {
                                                    Notification::make()
                                                        ->title('Sending test data to Influxdb')
                                                        ->info()
                                                        ->send();

                                                    TestConnectionJob::dispatch(Auth::user());
                                                })
                                                ->color('primary')
                                                ->icon('heroicon-o-check-circle')
                                                ->visible(fn (): bool => app(DataIntegrationSettings::class)->influxdb_v2_enabled),
                                        ]),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
