<?php

namespace App\Filament\Pages\Settings;

use App\Jobs\InfluxDBv2\WriteCompletedSpeedtest;
use App\Models\Result;
use App\Settings\DataIntegrationSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Artisan;

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

    /**
     * Method to handle sending old data to InfluxDB.
     */
    public function sendAllResultsToInfluxDB(): void
    {
        $DataIntegrationSettings = app(DataIntegrationSettings::class);

        if (! $DataIntegrationSettings->influxdb_v2_enabled) {
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
            WriteCompletedSpeedtest::dispatch($result, $DataIntegrationSettings);
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
        $DataIntegrationSettings = app(DataIntegrationSettings::class);

        if (! $DataIntegrationSettings->influxdb_v2_enabled) {
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
            ->body('A test log has been sent to InfluxDB, Check in InfluxDB if the data is received!')
            ->success()
            ->send();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'md' => 3,
                ])
                    ->schema([
                        Forms\Components\Section::make('InfluxDB v2')
                            ->description('When enabled, all new Speedtest results will also be sent to InfluxDB.')
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
                                            Forms\Components\Actions\Action::make('Send All Previous Results to Influxdb')
                                                ->label('Send All Previous Results to Influxdb')
                                                ->action('sendAllResultsToInfluxDB')
                                                ->color('primary')
                                                ->icon('heroicon-o-cloud-arrow-up')
                                                ->visible(fn (): bool => app(DataIntegrationSettings::class)->influxdb_v2_enabled),
                                        ]),
                                        // Button to test InfluxDB connection
                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('Test InfluxDB Connection')
                                                ->label('Test InfluxDB Connection')
                                                ->action('testInfluxDB')
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
                    ]),
            ]);
    }
}
