<?php

namespace App\Filament\Pages\Settings;

use App\Settings\GeneralSettings;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class GeneralPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static ?int $navigationSort = 2;

    protected static string $settings = GeneralSettings::class;

    public static function getNavigationGroup(): string
    {
        return __('translations.settings');
    }

    public function getTitle(): string
    {
        return __('translations.general_settings.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('translations.general_settings.label');
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

    protected function getActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->action('save'),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('translations.general_settings.label'))
                    ->description(__('translations.general_settings.description'))
                    ->schema([
                        Grid::make()
                            ->schema([
                                Section::make(__('translations.general_settings.app_settings'))
                                    ->schema([
                                        TextInput::make('app_name')
                                            ->label(__('translations.general_settings.app_name'))
                                            ->placeholder('Speedtest tracker')
                                            ->maxLength(255),
                                        TextInput::make('asset_url')
                                            ->label(__('translations.general_settings.asset_url'))
                                            ->maxLength(255),
                                        TextInput::make('app_timezone')
                                            ->label(__('translations.general_settings.app_timezone'))
                                            ->placeholder('Speedtest tracker')
                                            ->maxLength(255),
                                        Checkbox::make('chart_begin_at_zero')
                                            ->inline()
                                            ->label(__('translations.general_settings.chart_begin_at_zero')),
                                        TextInput::make('chart_datetime_format')
                                            ->label(__('translations.general_settings.chart_datetime_format')),
                                        TextInput::make('datetime_format')
                                            ->label(__('translations.general_settings.datetime_format'))
                                            ->maxLength(255),
                                        TextInput::make('display_timezone')
                                            ->label(__('translations.general_settings.display_timezone'))
                                            ->maxLength(255),
                                    ]),
                                Section::make(__('translations.general_settings.speedtest_settings'))
                                    ->schema([
                                        Checkbox::make('public_dashboard')
                                            ->inline()
                                            ->label(__('translations.general_settings.public_dashboard')),
                                        TextInput::make('speedtest_skip_ips')
                                            ->label(__('translations.general_settings.speedtest_skip_ips'))
                                            ->maxLength(255),
                                        TagsInput::make('speedtest_schedule')
                                            ->label(__('translations.general_settings.speedtest_schedule'))
                                            ->helperText(new HtmlString(__('translations.general_settings.speedtest_schedule_description')))
                                            ->placeholder('* * * * *')
                                            ->separator(','),
                                        TextInput::make('speedtest_servers')
                                            ->label(__('translations.general_settings.speedtest_servers'))
                                            ->maxLength(255),
                                        TextInput::make('speedtest_blocked_servers')
                                            ->label(__('translations.general_settings.speedtest_blocked_servers'))
                                            ->maxLength(255),
                                        TextInput::make('speedtest_interface')
                                            ->label(__('translations.general_settings.speedtest_interface'))
                                            ->placeholder('Speedtest tracker')
                                            ->maxLength(255),
                                        TextInput::make('speedtest_checkinternet_url')
                                            ->label(__('translations.general_settings.speedtest_checkinternet_url'))
                                            ->maxLength(255),
                                        Checkbox::make('threshold_enabled')
                                            ->inline()
                                            ->label(__('translations.general_settings.threshold_enabled')),
                                        TextInput::make('threshold_download')
                                            ->label(__('translations.general_settings.threshold_download'))
                                            ->maxLength(255),
                                        TextInput::make('threshold_upload')
                                            ->label(__('translations.general_settings.threshold_upload'))
                                            ->placeholder('Speedtest tracker')
                                            ->maxLength(255),
                                        TextInput::make('threshold_ping')
                                            ->label(__('translations.general_settings.threshold_ping'))
                                            ->maxLength(255),
                                        TextInput::make('prune_results_older_than')
                                            ->label(__('translations.general_settings.prune_results_older_than'))
                                            ->placeholder('Speedtest tracker')
                                            ->maxLength(255),
                                    ]),
                                Section::make(__('translations.general_settings.api_settings'))
                                    ->schema([
                                        TextInput::make('api_rate_limit')
                                            ->label(__('translations.general_settings.api_rate_limit'))
                                            ->maxLength(255),
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
