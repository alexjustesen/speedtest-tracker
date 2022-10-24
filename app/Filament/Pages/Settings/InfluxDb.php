<?php

namespace App\Filament\Pages\Settings;

use App\Settings\InfluxDbSettings;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;

class InfluxDb extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-database';

    protected static ?string $navigationLabel = 'InfluxDB';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 3;

    protected static string $settings = InfluxDbSettings::class;

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
                            ->collapsible()
                            ->schema([
                                TextInput::make('v2_url')
                                    ->label('URL')
                                    ->placeholder('http://your-influxdb-instance')
                                    ->maxLength(255)
                                    ->columnSpan(['md' => 2]),
                                TextInput::make('v2_org')
                                    ->label('Org')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                TextInput::make('v2_bucket')
                                    ->placeholder('speedtest-tracker')
                                    ->label('Bucket')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                TextInput::make('v2_token')
                                    ->label('Token')
                                    ->maxLength(255)
                                    ->password()
                                    ->disableAutocomplete()
                                    ->columnSpan(['md' => 2]),
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                            ]),
                    ])
                    ->columnSpan([
                        'md' => 2,
                    ]),

                    Card::make()
                        ->schema([
                            Toggle::make('v2_enabled')
                                ->label('v2 enabled')
                                ->helperText('NOTE: At this time only InfluxDB v2 is supported'),
                        ])
                        ->columnSpan([
                            'md' => 1,
                        ]),
                ]),
        ];
    }
}
