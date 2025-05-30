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
use Illuminate\Support\Facades\Auth;

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
        return Auth::check() && Auth::user()->is_admin;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'default' => 1,
                    'md' => 3,
                ])
                    ->schema([
                        Section::make('InfluxDB v2')
                            ->description('When enabled, all new Speedtest results will also be sent to InfluxDB.')
                            ->schema([
                                Forms\Components\Toggle::make('influxdb_v2_enabled')
                                    ->label('Enable')
                                    ->reactive()
                                    ->columnSpanFull(),
                                Grid::make(['default' => 1, 'md' => 3])
                                    ->hidden(fn (Forms\Get $get) => $get('influxdb_v2_enabled') !== true)
                                    ->schema([
                                        TextInput::make('influxdb_v2_url')
                                            ->label('URL')
                                            ->placeholder('http://your-influxdb-instance')
                                            ->maxLength(255)
                                            ->required(fn (Forms\Get $get) => $get('influxdb_v2_enabled') === true)
                                            ->columnSpan(['md' => 1]),
                                        TextInput::make('influxdb_v2_org')
                                            ->label('Org')
                                            ->maxLength(255)
                                            ->required(fn (Forms\Get $get) => $get('influxdb_v2_enabled') === true)
                                            ->columnSpan(['md' => 1]),
                                        TextInput::make('influxdb_v2_bucket')
                                            ->placeholder('speedtest-tracker')
                                            ->label('Bucket')
                                            ->maxLength(255)
                                            ->required(fn (Forms\Get $get) => $get('influxdb_v2_enabled') === true)
                                            ->columnSpan(['md' => 2]),
                                        TextInput::make('influxdb_v2_token')
                                            ->label('Token')
                                            ->maxLength(255)
                                            ->password()
                                            ->required(fn (Forms\Get $get) => $get('influxdb_v2_enabled') === true)
                                            ->disableAutocomplete()
                                            ->columnSpan(['md' => 2]),
                                        Checkbox::make('influxdb_v2_verify_ssl')
                                            ->label('Verify SSL')
                                            ->columnSpanFull(),
                                        // Button to send old data to InfluxDB
                                        Actions::make([
                                            Action::make('Export current results')
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
                                        Actions::make([
                                            Action::make('Test connection')
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
