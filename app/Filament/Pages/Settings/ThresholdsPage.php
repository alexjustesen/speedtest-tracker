<?php

namespace App\Filament\Pages\Settings;

use App\Settings\GeneralSettings;
use App\Settings\ThresholdSettings;
use Closure;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\View;
use Filament\Pages\SettingsPage;

class ThresholdsPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-exclamation';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'Thresholds';

    protected static ?string $navigationLabel = 'Thresholds';

    protected static string $settings = ThresholdSettings::class;

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
                                ->hidden(fn (Closure $get) => $get('absolute_enabled') !== true)
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
                                                ->hint('Ms')
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

                    Card::make()
                        ->schema([
                            View::make('filament.forms.thresholds-helptext'),
                        ])
                        ->columnSpan([
                            'md' => 1,
                        ]),
                ]),
        ];
    }
}
