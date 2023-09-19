<?php

namespace App\Filament\Pages\Settings;

use App\Settings\InfluxDbSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class InfluxDbPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'InfluxDB';

    protected static ?string $navigationLabel = 'InfluxDB';

    protected static string $settings = InfluxDbSettings::class;

    public function mount(): void
    {
        parent::mount();

        abort_unless(auth()->user()->is_admin, 403);
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
                ])
                    ->schema([
                        Forms\Components\Section::make('InfluxDB v2 Settings')
                            ->schema([
                                Forms\Components\Toggle::make('v2_enabled')
                                    ->label('Enable')
                                    ->reactive()
                                    ->columnSpan(2),
                                Forms\Components\Grid::make([
                                    'default' => 1,
                                    'md' => 3,
                                ])
                                    ->hidden(fn (Forms\Get $get) => $get('v2_enabled') !== true)
                                    ->schema([
                                        Forms\Components\TextInput::make('v2_url')
                                            ->label('URL')
                                            ->placeholder('http://your-influxdb-instance')
                                            ->maxLength(255)
                                            ->required(fn (Forms\Get $get) => $get('v2_enabled') == true)
                                            ->columnSpanFull(),
                                        Forms\Components\Checkbox::make('v2_verify_ssl')
                                            ->label('Verify SSL')
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('v2_org')
                                            ->label('Org')
                                            ->maxLength(255)
                                            ->required(fn (Forms\Get $get) => $get('v2_enabled') == true)
                                            ->columnSpan(['md' => 2]),
                                        Forms\Components\TextInput::make('v2_bucket')
                                            ->placeholder('speedtest-tracker')
                                            ->label('Bucket')
                                            ->maxLength(255)
                                            ->required(fn (Forms\Get $get) => $get('v2_enabled') == true)
                                            ->columnSpan(['md' => 1]),
                                        Forms\Components\TextInput::make('v2_token')
                                            ->label('Token')
                                            ->maxLength(255)
                                            ->password()
                                            ->required(fn (Forms\Get $get) => $get('v2_enabled') == true)
                                            ->disableAutocomplete()
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->compact()
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                            ]),
                    ])
                    ->columnSpan('full'),
            ]);
    }
}
