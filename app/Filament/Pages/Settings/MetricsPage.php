<?php

namespace App\Filament\Pages\Settings;

use App\Settings\MetricsSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'md' => 3,
                ])
                    ->schema([

                        // Prometheus Section
                        Forms\Components\Section::make('Prometheus Settings')
                            ->schema([
                                Forms\Components\Toggle::make('prometheus_enabled')
                                    ->label('Enable Metrics Endpoint'),
                            ])
                            ->columnSpan(3), // Adjust columnSpan to fit width

                        // InfluxDB Section
                        Forms\Components\Section::make('InfluxDB v2 Settings')
                            ->schema([
                                Forms\Components\Toggle::make('influxdb_v2_enabled')
                                    ->label('Enable')
                                    ->reactive()
                                    ->columnSpan(2), // Make sure the toggle spans the same width as Prometheus section
                                Forms\Components\Grid::make([
                                    'default' => 1,
                                    'md' => 3,
                                ])
                                    ->hidden(fn (Forms\Get $get) => $get('influxdb_v2_enabled') !== true)
                                    ->schema([
                                        Forms\Components\TextInput::make('influxdb_v2_url')
                                            ->label('URL')
                                            ->placeholder('http://your-influxdb-instance')
                                            ->maxLength(255)
                                            ->required(fn (Forms\Get $get) => $get('influxdb_v2_enabled') == true)
                                            ->columnSpanFull(),
                                        Forms\Components\Checkbox::make('influxdb_v2_verify_ssl')
                                            ->label('Verify SSL')
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('influxdb_v2_org')
                                            ->label('Org')
                                            ->maxLength(255)
                                            ->required(fn (Forms\Get $get) => $get('influxdb_v2_enabled') == true)
                                            ->columnSpan(['md' => 2]),
                                        Forms\Components\TextInput::make('influxdb_v2_bucket')
                                            ->placeholder('speedtest-tracker')
                                            ->label('Bucket')
                                            ->maxLength(255)
                                            ->required(fn (Forms\Get $get) => $get('influxdb_v2_enabled') == true)
                                            ->columnSpan(['md' => 1]),
                                        Forms\Components\TextInput::make('influxdb_v2_token')
                                            ->label('Token')
                                            ->maxLength(255)
                                            ->password()
                                            ->required(fn (Forms\Get $get) => $get('influxdb_v2_enabled') == true)
                                            ->disableAutocomplete()
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->compact()
                            ->columns([
                                'default' => 1,
                                'md' => 3,
                            ]),
                    ]),
            ]);
    }
}
