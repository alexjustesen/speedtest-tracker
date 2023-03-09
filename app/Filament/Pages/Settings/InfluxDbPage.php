<?php

namespace App\Filament\Pages\Settings;

use App\Settings\GeneralSettings;
use App\Settings\InfluxDbSettings;
use Closure;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;

class InfluxDbPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-database';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'InfluxDB';

    protected static ?string $navigationLabel = 'InfluxDB';

    protected static string $settings = InfluxDbSettings::class;

    protected function getMaxContentWidth(): string
    {
        $settings = new GeneralSettings();

        return $settings->content_width;
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make([
                'default' => 1,
                'md' => 3,
            ])
                ->schema([
                    Grid::make([
                        'default' => 1,
                    ])
                    ->schema([
                        Section::make('InfluxDB v2 Settings')
                            ->schema([
                                Toggle::make('v2_enabled')
                                    ->label('Enable')
                                    ->reactive()
                                    ->columnSpan(2),
                                Grid::make([
                                    'default' => 1,
                                ])
                                    ->hidden(fn (Closure $get) => $get('v2_enabled') !== true)
                                    ->schema([
                                        TextInput::make('v2_url')
                                            ->label('URL')
                                            ->placeholder('http://your-influxdb-instance')
                                            ->maxLength(255)
                                            ->required(fn (Closure $get) => $get('v2_enabled') == true)
                                            ->columnSpan(['md' => 2]),
                                        Checkbox::make('v2_verify_ssl')
                                            ->label('Verify SSL')
                                            ->columnSpan(['md' => 2]),
                                        TextInput::make('v2_org')
                                            ->label('Org')
                                            ->maxLength(255)
                                            ->required(fn (Closure $get) => $get('v2_enabled') == true)
                                            ->columnSpan(1),
                                        TextInput::make('v2_bucket')
                                            ->placeholder('speedtest-tracker')
                                            ->label('Bucket')
                                            ->maxLength(255)
                                            ->required(fn (Closure $get) => $get('v2_enabled') == true)
                                            ->columnSpan(1),
                                        TextInput::make('v2_token')
                                            ->label('Token')
                                            ->maxLength(255)
                                            ->password()
                                            ->required(fn (Closure $get) => $get('v2_enabled') == true)
                                            ->disableAutocomplete()
                                            ->columnSpan(['md' => 2]),
                                    ]),
                            ])
                            ->compact()
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                            ]),
                    ])
                    ->columnSpan([
                        'md' => 2,
                    ]),
                ]),
        ];
    }
}
