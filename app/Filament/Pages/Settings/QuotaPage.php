<?php

namespace App\Filament\Pages\Settings;

use App\Settings\QuotaSettings;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;

class QuotaPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'tabler-antenna-bars-3';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'Quota';

    protected static ?string $navigationLabel = 'Quota';

    protected static string $settings = QuotaSettings::class;

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'default' => 1,
                ])->schema([
                    Section::make('Settings')
                        ->headerActions([
                            Action::make('Documentation')
                                ->icon('tabler-books')
                                ->iconButton()
                                ->color('gray')
                                ->url('https://docs.speedtest-tracker.dev/')
                                ->openUrlInNewTab(),
                        ])
                        ->schema([
                            Toggle::make('enabled')
                                ->label('Enable quota tracking')
                                ->helperText(fn (): ?string => config('speedtest.quota_enabled') ? 'Quota is being configured using environment variables, UI control has been disabled.' : null)
                                ->reactive()
                                ->disabled(fn (): bool => config('speedtest.quota_enabled'))
                                ->columnSpanFull(),

                            TextInput::make('size')
                                ->helperText('Specify the quota size, e.g., "500 GB" or "1 TB".')
                                ->required()
                                ->disabled(fn (): bool => config('speedtest.quota_enabled')),

                            Select::make('period')
                                ->options([
                                    'day' => 'Day',
                                    'week' => 'Week',
                                    'month' => 'Month',
                                ])
                                ->required()
                                ->disabled(fn (): bool => config('speedtest.quota_enabled')),

                            TextInput::make('reset_day')
                                ->helperText('Specify the day of the month or day of the week for the quota to reset.')
                                ->required()
                                ->minValue(0)
                                ->maxValue(31)
                                ->numeric()
                                ->disabled(fn (): bool => config('speedtest.quota_enabled')),
                        ]),
                ]),
            ]);
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::TwoExtraLarge;
    }

    public static function getNavigationBadge(): ?string
    {
        return config('speedtest.quota_enabled') ? 'Disabled' : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return config('speedtest.quota_enabled') ? 'gray' : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return config('speedtest.quota_enabled') ? 'Quota is enabled as .env variable' : null;
    }
}
