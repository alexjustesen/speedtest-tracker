<?php

namespace App\Filament\Pages\Settings;

use App\Settings\GeneralSettings;
use BackedEnum;
use CodeWithDennis\SimpleAlert\Components\SimpleAlert;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

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
                        Tab::make('Bandwidth')
                            ->icon('tabler-cloud-data-connection')
                            ->columns([
                                'default' => 1,
                                'md' => 3,
                            ])
                            ->schema([
                                SimpleAlert::make('data_usage_note')
                                    ->info()
                                    ->description('Data usage is calculated based on the total downloaded and uploaded data from Ookla Speedtests only.')
                                    ->columnSpanFull(),

                                Toggle::make('data_cap_enabled')
                                    ->label('Enable bandwidth data cap')
                                    ->reactive()
                                    ->columnSpanFull(),

                                Grid::make([
                                    'default' => 1,
                                    'md' => 2,
                                ])
                                    ->schema([
                                        TextInput::make('data_cap_data_limit')
                                            ->label('Data limit')
                                            ->placeholder('e.g., 500GB, 1TB')
                                            ->required(fn (Get $get) => $get('data_cap_enabled'))
                                            ->columnSpanFull(),

                                        TextInput::make('data_cap_warning_threshold')
                                            ->label('Warning threshold')
                                            ->numeric()
                                            ->integer()
                                            ->minValue(1)
                                            ->maxValue(100)
                                            ->required()
                                            ->suffix('%'),

                                        Select::make('data_cap_action')
                                            ->label('Action on limit reached')
                                            ->options([
                                                'notify' => 'Notify admin(s)',
                                                'block' => 'Block and notify admin(s)',
                                            ])
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
                                        Fieldset::make('period_settings')
                                            ->label('Period settings')
                                            ->schema([
                                                Select::make('data_cap_period')
                                                    ->label('Period')
                                                    ->options([
                                                        'day' => 'Day',
                                                        'week' => 'Week',
                                                        'month' => 'Month',
                                                    ])
                                                    ->required(),

                                                TextInput::make('data_cap_reset_day')
                                                    ->label('Reset day')
                                                    ->numeric()
                                                    ->integer()
                                                    ->minValue(0)
                                                    ->maxValue(31)
                                                    ->required(),
                                            ])
                                            ->columns(1),
                                    ])
                                    ->columnSpan([
                                        'default' => 1,
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
