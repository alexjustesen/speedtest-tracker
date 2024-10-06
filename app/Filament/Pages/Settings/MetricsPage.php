<?php

namespace App\Filament\Pages\Settings;

use App\Jobs\InfluxDBv2\WriteCompletedSpeedtest;
use App\Models\Result;
use App\Settings\MetricsSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Artisan;

class MetricsPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Metrics Export Settings';

    protected static ?string $navigationLabel = 'Metrics Export';

    protected static string $settings = MetricsSettings::class;

    public static function canAccess(): bool
    {
        return auth()->user()->is_admin;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->is_admin;
    }

    /**
     * Method to handle sending old data to InfluxDB.
     */
    public function sendAllResultsToInfluxDB(): void
    {
        $metricsSettings = app(MetricsSettings::class);

        if (! $metricsSettings->influxdb_v2_enabled) {
            Notification::make()
                ->title('Error')
                ->body('InfluxDB is not enabled. Please enable InfluxDB in settings first.')
                ->danger()
                ->send();

            return;
        }

        // Fetch all results that need to be sent to InfluxDB
        $results = Result::where('status', 'completed')->get();

        foreach ($results as $result) {
            WriteCompletedSpeedtest::dispatch($result, $metricsSettings);
        }

        Notification::make()
            ->title('Success')
            ->body('All old results have been dispatched to InfluxDB successfully!')
            ->success()
            ->send();
    }

    /**
     * Method to test InfluxDB connection by writing a test log.
     */
    public function testInfluxDB(): void
    {
        $metricsSettings = app(MetricsSettings::class);

        if (! $metricsSettings->influxdb_v2_enabled) {
            Notification::make()
                ->title('Error')
                ->body('InfluxDB is not enabled. Please enable InfluxDB in settings first.')
                ->danger()
                ->send();

            return;
        }

        // Execute the TestInfluxDB command
        Artisan::call('app:test-influxdb');

        Notification::make()
            ->title('Success')
            ->body('A test log has been sent to InfluxDB successfully!')
            ->success()
            ->send();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('metrics_tabs')
                    ->columnSpanFull()
                    ->tabs([
                        // Prometheus Tab
                        Forms\Components\Tabs\Tab::make('Prometheus')
                            ->schema([
                                Forms\Components\Toggle::make('prometheus_enabled')
                                    ->label('Enable')
                                    ->columnSpanFull(),
                            ]),

                        // InfluxDB Tab
                        Forms\Components\Tabs\Tab::make('InfluxDB v2')
                            ->schema([
                                Forms\Components\Toggle::make('influxdb_v2_enabled')
                                    ->label('Enable')
                                    ->reactive()
                                    ->columnSpanFull(),
                                Forms\Components\Grid::make(['default' => 1, 'md' => 3])
                                    ->hidden(fn (Forms\Get $get) => $get('influxdb_v2_enabled') !== true)
                                    ->schema([
                                        Forms\Components\TextInput::make('influxdb_v2_url')
                                            ->label('URL')
                                            ->placeholder('http://your-influxdb-instance')
                                            ->maxLength(255)
                                            ->required(fn (Forms\Get $get) => $get('influxdb_v2_enabled') == true)
                                            ->columnSpan(['md' => 1]),
                                        Forms\Components\TextInput::make('influxdb_v2_org')
                                            ->label('Org')
                                            ->maxLength(255)
                                            ->required(fn (Forms\Get $get) => $get('influxdb_v2_enabled') == true)
                                            ->columnSpan(['md' => 1]),
                                        Forms\Components\TextInput::make('influxdb_v2_bucket')
                                            ->placeholder('speedtest-tracker')
                                            ->label('Bucket')
                                            ->maxLength(255)
                                            ->required(fn (Forms\Get $get) => $get('influxdb_v2_enabled') == true)
                                            ->columnSpan(['md' => 2]),
                                        Forms\Components\TextInput::make('influxdb_v2_token')
                                            ->label('Token')
                                            ->maxLength(255)
                                            ->password()
                                            ->required(fn (Forms\Get $get) => $get('influxdb_v2_enabled') == true)
                                            ->disableAutocomplete()
                                            ->columnSpan(['md' => 2]),
                                        Forms\Components\Checkbox::make('influxdb_v2_verify_ssl')
                                            ->label('Verify SSL')
                                            ->columnSpanFull(),
                                        // Button to send old data to InfluxDB
                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('Send All Previous Results to Influxdb')
                                                ->label('Send All Previous Results to Influxdb')
                                                ->action('sendAllResultsToInfluxDB')
                                                ->color('primary')
                                                ->icon('heroicon-o-cloud-arrow-up')
                                                ->visible(fn (): bool => app(MetricsSettings::class)->influxdb_v2_enabled),
                                        ]),
                                        // Button to test InfluxDB connection
                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('Test InfluxDB Connection')
                                                ->label('Test InfluxDB Connection')
                                                ->action('testInfluxDB')
                                                ->color('primary')
                                                ->icon('heroicon-o-check-circle')
                                                ->visible(fn (): bool => app(MetricsSettings::class)->influxdb_v2_enabled),
                                        ]),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ]),
            ])
            ->columns(1); // Sets the entire form to one column layout to use full width
    }
}
