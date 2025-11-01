<?php

namespace App\Filament\Pages\Settings;

use App\Settings\ThresholdSettings;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class Thresholds extends SettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'default' => 1,
                    'md' => 3,
                ])
                    ->columnSpan('full')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                        ])
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
                                            ->hidden(fn (Get $get) => $get('absolute_enabled') !== true)
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
                                    ->columnSpan('full'),
                            ])
                            ->columnSpan([
                                'md' => 2,
                            ]),
                    ]),
            ]);
    }
}