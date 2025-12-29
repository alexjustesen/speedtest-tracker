<?php

namespace App\Filament\Pages\Settings;

use App\Settings\GeneralSettings;
use BackedEnum;
use CodeWithDennis\SimpleAlert\Components\SimpleAlert;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class General extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = 'tabler-adjustments-cog';

    protected static string $settings = GeneralSettings::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 0;

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
            ->columns(1)
            ->components([
                Tabs::make()
                    ->schema([
                        Tab::make('Data Usage')
                            ->icon('tabler-cloud-data-connection')
                            ->columns([
                                'default' => 1,
                                'md' => 3,
                            ])
                            ->schema([
                                SimpleAlert::make('data_usage_note')
                                    ->info()
                                    ->description(new HtmlString('Data usage is calculated on the total downloaded and uploaded data from Ookla Speedtests <u>only</u>.'))
                                    ->columnSpanFull(),

                                Toggle::make('data_usage_enabled')
                                    ->label('Enable data usage limit')
                                    ->reactive()
                                    ->columnSpanFull(),

                                Grid::make([
                                    'default' => 1,
                                    'md' => 2,
                                ])
                                    ->schema([
                                        TextInput::make('data_usage_limit')
                                            ->label('Usage limit')
                                            ->placeholder('e.g., 500GB, 1TB')
                                            ->required(fn (Get $get) => $get('data_usage_enabled'))
                                            ->columnSpanFull(),

                                        Select::make('data_usage_period')
                                            ->label('Period')
                                            ->options([
                                                'day' => 'Day',
                                                'week' => 'Week',
                                                'month' => 'Month',
                                            ])
                                            ->required(),

                                        TextInput::make('data_usage_reset_day')
                                            ->label('Reset day')
                                            ->helperText('Which day of the month or week (Sunday=0, Monday=1 etc.) should the data usage reset?')
                                            ->numeric()
                                            ->integer()
                                            ->step(1)
                                            ->minValue(0)
                                            ->maxValue(31)
                                            ->required(),
                                    ])
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 2,
                                    ]),

                                Grid::make([
                                    'default' => 1,
                                ])
                                    ->schema([
                                        Radio::make('data_usage_action')
                                            ->label('Action on limit reached')
                                            ->options([
                                                'notify' => 'Notify',
                                                'block' => 'Block and notify',
                                            ])
                                            ->descriptions([
                                                'notify' => 'Allow speed tests but notify the admin user(s) when the limit is reached.',
                                                'block' => 'Block further speed tests and notify the admin user(s) when the limit is reached.',
                                            ]),
                                    ])
                                    ->columnSpan([
                                        'default' => 1,
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
