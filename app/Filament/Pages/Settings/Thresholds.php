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
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class Thresholds extends SettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'tabler-alert-triangle';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 4;

    public function getTitle(): string
    {
        return __('settings/thresholds.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('settings/thresholds.label');
    }

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
                                Section::make(__('settings/thresholds.absolute'))
                                    ->description(__('settings/thresholds.absolute_description'))
                                    ->schema([
                                        Toggle::make('absolute_enabled')
                                            ->label(__('settings/thresholds.absolute_enabled'))
                                            ->reactive()
                                            ->columnSpan(2),
                                        Grid::make([
                                            'default' => 1,
                                        ])
                                            ->hidden(fn (Get $get) => $get('absolute_enabled') !== true)
                                            ->schema([
                                                Fieldset::make(__('settings/thresholds.metrics'))
                                                    ->schema([
                                                        TextInput::make('absolute_download')
                                                            ->label(__('general.download'))
                                                            ->hint(__('general.mbps'))
                                                            ->helperText(__('settings/thresholds.metrics_helper_text'))
                                                            ->default(0)
                                                            ->minValue(0)
                                                            ->numeric()
                                                            ->required(),
                                                        TextInput::make('absolute_upload')
                                                            ->label(__('general.upload'))
                                                            ->hint(__('general.mbps'))
                                                            ->helperText(__('settings/thresholds.metrics_helper_text'))
                                                            ->default(0)
                                                            ->minValue(0)
                                                            ->numeric()
                                                            ->required(),
                                                        TextInput::make('absolute_ping')
                                                            ->label(__('general.ping'))
                                                            ->hint(__('general.ms'))
                                                            ->helperText(__('settings/thresholds.metrics_helper_text'))
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
