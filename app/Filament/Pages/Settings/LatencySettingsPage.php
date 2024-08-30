<?php

namespace App\Filament\Pages\Settings;

use App\Settings\LatencySettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class LatencySettingsPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 5;

    protected static ?string $title = 'Latency Settings';

    protected static ?string $navigationLabel = 'Latency Settings';

    protected static string $settings = LatencySettings::class;

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
                    'md' => 1,
                ])

                    ->schema([
                        Forms\Components\Section::make('General')
                            ->schema([
                                Forms\Components\Toggle::make('latency_enabled')
                                    ->label('Enable Latency Tests')
                                    ->default(false) // Set default value as needed
                                    ->reactive(), // Ensure the form reacts to changes

                                // Conditionally show fields based on the 'latency_enabled' toggle
                                Forms\Components\Grid::make([
                                    'default' => 1,
                                    'md' => 2,
                                ])
                                    ->hidden(fn (Forms\Get $get) => $get('latency_enabled') !== true)
                                    ->schema([
                                        Forms\Components\TextInput::make('ping_count')
                                            ->label('Ping Count')
                                            ->helperText('Number of pings to send during the test.')
                                            ->default(10)
                                            ->minValue(1)
                                            ->numeric()
                                            ->required(),
                                        Forms\Components\TextInput::make('latency_schedule')
                                            ->label('Cron Expression')
                                            ->helperText('Specify the cron expression for scheduling tests.')
                                            ->default('0 0 * * *') // Set default cron expression
                                            ->required(),
                                        Forms\Components\Select::make('latency_column_span')  // Add a select for column_span
                                            ->label('View')
                                            ->options([
                                                'full' => 'List view',
                                                'half' => 'Grid view',
                                            ])
                                            ->default('full')
                                            ->required(),
                                    ]),
                            ])
                            ->compact()
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                            ]),

                        Forms\Components\Section::make('Targets')
                            ->hidden(fn (Forms\Get $get) => $get('latency_enabled') !== true)
                            ->schema([
                                Forms\Components\Repeater::make('target_url')
                                    ->label('Targets')
                                    ->schema([
                                        Forms\Components\TextInput::make('target_name')
                                            ->label('Display Name')
                                            ->placeholder('Enter a display name')
                                            ->maxLength(100)
                                            ->required(),
                                        Forms\Components\TextInput::make('url')
                                            ->label('Target')
                                            ->placeholder('example.com')
                                            ->maxLength(2000)
                                            ->required(),
                                    ])
                                    ->columns([
                                        'default' => 1,
                                        'md' => 2,
                                    ]),
                            ]),
                    ]),
            ]);
    }
}