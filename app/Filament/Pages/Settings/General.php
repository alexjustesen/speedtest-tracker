<?php

namespace App\Filament\Pages\Settings;

use App\Settings\GeneralSettings;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Squire\Models\Timezone;

class General extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    protected static string $settings = GeneralSettings::class;

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
                        Section::make('Site Settings')
                            ->collapsible()
                            ->schema([
                                TextInput::make('site_name')
                                    ->maxLength(50)
                                    ->required()
                                    ->columnSpan(['md' => 2]),
                                Select::make('timezone')
                                    ->options(Timezone::all()->pluck('code', 'code'))
                                    ->searchable()
                                    ->required()
                                    ->columnSpan(1),
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                            ]),

                        Section::make('Speedtest Settings')
                            ->collapsible()
                            ->schema([
                                TextInput::make('speedtest_schedule')
                                    ->helperText('Leave empty to disable the schedule. You can also use the cron expression generator [HERE](https://crontab.cronhub.io/) to help you make schedules.')
                                    ->nullable()
                                    ->columnSpan(1),
                                TextInput::make('speedtest_server')
                                    ->helperText('Leave empty to let the system pick the best server.')
                                    ->nullable()
                                    ->columnSpan(1),
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
                            Toggle::make('auth_enabled')
                                ->label('Authentication enabled')
                                ->helperText("NOTE: Authentication is currently required. It's on the roadmap to be able to disabled it though.")
                                ->disabled(),
                        ])
                        ->columnSpan([
                            'md' => 1,
                        ]),
                ]),
        ];
    }
}
