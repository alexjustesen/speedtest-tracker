<?php

namespace App\Filament\Pages\Settings;

use App\Settings\ThresholdSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ThresholdsPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'Thresholds';

    protected static ?string $navigationLabel = 'Thresholds';

    protected static string $settings = ThresholdSettings::class;

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
                        Forms\Components\Grid::make([
                            'default' => 1,
                        ])
                            ->schema([
                                Forms\Components\Section::make('Absolute')
                                    ->description('Absolute thresholds do not take into account previous history and could be triggered on each test.')
                                    ->schema([
                                        Forms\Components\Toggle::make('absolute_enabled')
                                            ->label('Enable absolute thresholds')
                                            ->reactive()
                                            ->columnSpan(2),
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                        ])
                                            ->hidden(fn (Forms\Get $get) => $get('absolute_enabled') !== true)
                                            ->schema([
                                                Forms\Components\Fieldset::make('Metrics')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('absolute_download')
                                                            ->label('Download')
                                                            ->hint('Mbps')
                                                            ->helperText('Set to zero to disable this metric.')
                                                            ->default(0)
                                                            ->minValue(0)
                                                            ->numeric()
                                                            ->required(),
                                                        Forms\Components\TextInput::make('absolute_upload')
                                                            ->label('Upload')
                                                            ->hint('Mbps')
                                                            ->helperText('Set to zero to disable this metric.')
                                                            ->default(0)
                                                            ->minValue(0)
                                                            ->numeric()
                                                            ->required(),
                                                        Forms\Components\TextInput::make('absolute_ping')
                                                            ->label('Ping')
                                                            ->hint('ms')
                                                            ->helperText('Set to zero to disable this metric.')
                                                            ->default(0)
                                                            ->minValue(0)
                                                            ->numeric()
                                                            ->required(),
                                                    ])
                                                    ->columns([
                                                        'default' => 1,
                                                        'md' => 2,
                                                    ]),
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

                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\View::make('filament.forms.thresholds-helptext'),
                            ])
                            ->columnSpan([
                                'md' => 1,
                            ]),
                    ]),
            ]);
    }
}
