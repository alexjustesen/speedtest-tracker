<?php

namespace App\Filament\Pages\Settings;

use App\Settings\ThresholdSettings;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;

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
        return Auth::check() && Auth::user()->is_admin;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::ThreeExtraLarge;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Absolute')
                    ->description('Absolute thresholds do not take into account previous history and could be triggered on each test.')
                    ->schema([
                        Toggle::make('absolute_enabled')
                            ->label('Enable absolute thresholds')
                            ->reactive()
                            ->columnSpan(2),
                        Grid::make([
                            'default' => 1,
                        ])
                            ->hidden(fn (Forms\Get $get) => $get('absolute_enabled') !== true)
                            ->schema([
                                Fieldset::make('Metrics')
                                    ->schema([
                                        TextInput::make('absolute_download')
                                            ->label('Download')
                                            ->hint('Mbps')
                                            ->helperText('Set to zero to disable this metric.')
                                            ->default(0)
                                            ->minValue(0)
                                            ->numeric()
                                            ->required(),

                                        TextInput::make('absolute_upload')
                                            ->label('Upload')
                                            ->hint('Mbps')
                                            ->helperText('Set to zero to disable this metric.')
                                            ->default(0)
                                            ->minValue(0)
                                            ->numeric()
                                            ->required(),

                                        TextInput::make('absolute_ping')
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
            ->columns([
                'default' => 1,
            ]);
    }
}
