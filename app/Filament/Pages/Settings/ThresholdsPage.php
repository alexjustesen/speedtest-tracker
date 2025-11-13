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

    protected static ?int $navigationSort = 4;

    protected static string $settings = ThresholdSettings::class;

    public static function getNavigationGroup(): string
    {
        return __('common.settings');
    }

    public function getTitle(): string
    {
        return __('thresholds.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('thresholds.label');
    }

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
                    ->description(__('thresholds.absolute_thresholds_description'))
                    ->schema([
                        Toggle::make('absolute_enabled')
                            ->label(__('thresholds.enable_absolute_thresholds'))
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
                                            ->label(__('common.download'))
                                            ->hint('Mbps')
                                            ->helperText(__('thresholds.help_text'))
                                            ->default(0)
                                            ->minValue(0)
                                            ->numeric()
                                            ->required(),

                                        TextInput::make('absolute_upload')
                                            ->label(__('common.upload'))
                                            ->hint('Mbps')
                                            ->helperText(__('thresholds.help_text'))
                                            ->default(0)
                                            ->minValue(0)
                                            ->numeric()
                                            ->required(),

                                        TextInput::make('absolute_ping')
                                            ->label(__('common.ping'))
                                            ->hint('ms')
                                            ->helperText(__('thresholds.help_text'))
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
