<?php

namespace App\Filament\Pages\Settings;

use App\Actions\Notifications\SendAppriseTestNotification;
use App\Actions\Notifications\SendDatabaseTestNotification;
use App\Actions\Notifications\SendMailTestNotification;
use App\Actions\Notifications\SendWebhookTestNotification;
use App\Rules\AppriseScheme;
use App\Rules\ContainsString;
use App\Settings\NotificationSettings;
use CodeWithDennis\SimpleAlert\Components\SimpleAlert;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class Notification extends SettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'tabler-bell-ringing';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 3;

    public function getTitle(): string
    {
        return __('settings/notifications.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('settings/notifications.label');
    }

    protected static string $settings = NotificationSettings::class;

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
                Tabs::make()
                    ->schema([
                        Tab::make(__('settings/notifications.database'))
                            ->icon(Heroicon::OutlinedCircleStack)
                            ->schema([
                                Toggle::make('database_enabled')
                                    ->label(__('general.enable'))
                                    ->live(),

                                Grid::make([
                                    'default' => 1,
                                ])
                                    ->hidden(fn (Get $get) => $get('database_enabled') !== true)
                                    ->schema([
                                        Fieldset::make(__('settings.triggers'))
                                            ->columns(1)
                                            ->schema([
                                                Checkbox::make('database_on_speedtest_run')
                                                    ->label(__('settings/notifications.notify_on_every_speedtest_run'))
                                                    ->helpertext(__('settings/notifications.notify_on_every_speedtest_run_helper')),
                                                Checkbox::make('database_on_threshold_failure')
                                                    ->label(__('settings/notifications.notify_on_threshold_failures'))
                                                    ->helpertext(__('settings/notifications.notify_on_threshold_failures_helper')),
                                            ]),

                                        Actions::make([
                                            Action::make('test database')
                                                ->label(__('settings/notifications.test_database_channel'))
                                                ->action(fn () => SendDatabaseTestNotification::run(user: Auth::user())),
                                        ]),
                                    ]),

                                // ...
                            ]),

                        Tab::make(__('settings/notifications.mail'))
                            ->icon(Heroicon::OutlinedEnvelope)
                            ->schema([
                                Toggle::make('mail_enabled')
                                    ->label(__('general.enable'))
                                    ->live(),

                                Grid::make([
                                    'default' => 1,
                                ])
                                    ->hidden(fn (Get $get) => $get('mail_enabled') !== true)
                                    ->schema([
                                        Fieldset::make(__('settings.triggers'))
                                            ->columns(1)
                                            ->schema([
                                                Checkbox::make('mail_on_speedtest_run')
                                                    ->label(__('settings/notifications.notify_on_every_speedtest_run'))
                                                    ->helpertext(__('settings/notifications.notify_on_every_speedtest_run_helper')),
                                                Checkbox::make('mail_on_threshold_failure')
                                                    ->label(__('settings/notifications.notify_on_threshold_failures'))
                                                    ->helpertext(__('settings/notifications.notify_on_threshold_failures_helper')),
                                            ]),

                                        Repeater::make('mail_recipients')
                                            ->label(__('settings/notifications.recipients'))
                                            ->schema([
                                                TextInput::make('email_address')
                                                    ->placeholder('your@email.com')
                                                    ->email()
                                                    ->required(),
                                            ]),

                                        Actions::make([
                                            Action::make('test mail')
                                                ->label(__('settings/notifications.test_mail_channel'))
                                                ->action(fn (Get $get) => SendMailTestNotification::run(recipients: $get('mail_recipients')))
                                                ->hidden(fn (Get $get) => ! count($get('mail_recipients'))),
                                        ]),
                                    ]),

                                // ...
                            ]),

                        Tab::make(__('settings/notifications.webhook'))
                            ->icon(Heroicon::OutlinedGlobeAlt)
                            ->schema([
                                SimpleAlert::make('wehbook_info')
                                    ->title(__('general.documentation'))
                                    ->description(__('settings/notifications.webhook_hint_description'))
                                    ->border()
                                    ->info()
                                    ->actions([
                                        Action::make('webhook_docs')
                                            ->label(__('general.view_documentation'))
                                            ->icon('heroicon-m-arrow-long-right')
                                            ->color('info')
                                            ->link()
                                            ->url('https://docs.speedtest-tracker.dev/settings/notifications/webhook')
                                            ->openUrlInNewTab(),
                                    ])
                                    ->columnSpanFull(),

                                Toggle::make('webhook_enabled')
                                    ->label(__('general.enable'))
                                    ->live(),

                                Grid::make([
                                    'default' => 1,
                                ])
                                    ->hidden(fn (Get $get) => $get('webhook_enabled') !== true)
                                    ->schema([
                                        Fieldset::make(__('settings.triggers'))
                                            ->columns(1)
                                            ->schema([
                                                Checkbox::make('webhook_on_speedtest_run')
                                                    ->label(__('settings/notifications.notify_on_every_speedtest_run'))
                                                    ->helpertext(__('settings/notifications.notify_on_every_speedtest_run_helper')),
                                                Checkbox::make('webhook_on_threshold_failure')
                                                    ->label(__('settings/notifications.notify_on_threshold_failures'))
                                                    ->helpertext(__('settings/notifications.notify_on_threshold_failures_helper')),
                                            ]),

                                        Repeater::make('webhook_urls')
                                            ->label(__('settings/notifications.recipients'))
                                            ->schema([
                                                TextInput::make('url')
                                                    ->placeholder('https://webhook.site/longstringofcharacters')
                                                    ->maxLength(2000)
                                                    ->required()
                                                    ->url(),
                                            ]),

                                        Actions::make([
                                            Action::make('test webhook')
                                                ->label(__('settings/notifications.test_webhook_channel'))
                                                ->action(fn (Get $get) => SendWebhookTestNotification::run(webhooks: $get('webhook_urls')))
                                                ->hidden(fn (Get $get) => ! count($get('webhook_urls'))),
                                        ]),
                                    ]),

                                // ...
                            ]),
                        Tab::make(__('settings/notifications.apprise'))
                            ->icon(Heroicon::CloudArrowUp)
                            ->schema([
                                SimpleAlert::make('wehbook_info')
                                    ->title(__('general.documentation'))
                                    ->description(__('settings/notifications.apprise_hint_description'))
                                    ->border()
                                    ->info()
                                    ->actions([
                                        Action::make('webhook_docs')
                                            ->label(__('general.view_documentation'))
                                            ->icon('heroicon-m-arrow-long-right')
                                            ->color('info')
                                            ->link()
                                            ->url('https://docs.speedtest-tracker.dev/settings/notifications/apprise')
                                            ->openUrlInNewTab(),
                                    ])
                                    ->columnSpanFull(),

                                Toggle::make('apprise_enabled')
                                    ->label(__('settings/notifications.enable_apprise_notifications'))
                                    ->reactive()
                                    ->columnSpanFull(),
                                Grid::make([
                                    'default' => 1,
                                ])
                                    ->hidden(fn (Get $get) => $get('apprise_enabled') !== true)
                                    ->schema([
                                        Fieldset::make(__('settings/notifications.apprise_server'))
                                            ->schema([
                                                TextInput::make('apprise_server_url')
                                                    ->label(__('settings/notifications.apprise_server_url'))
                                                    ->placeholder('http://localhost:8000/notify')
                                                    ->helperText(__('settings/notifications.apprise_server_url_helper'))
                                                    ->maxLength(2000)
                                                    ->required()
                                                    ->url()
                                                    ->rule(new ContainsString('/notify'))
                                                    ->columnSpanFull(),
                                                Checkbox::make('apprise_verify_ssl')
                                                    ->label(__('settings/notifications.apprise_verify_ssl'))
                                                    ->default(true)
                                                    ->columnSpanFull(),
                                            ]),
                                        Fieldset::make(__('settings.triggers'))
                                            ->schema([
                                                Checkbox::make('apprise_on_speedtest_run')
                                                    ->label(__('settings/notifications.notify_on_every_speedtest_run'))
                                                    ->helpertext(__('settings/notifications.notify_on_every_speedtest_run_helper'))
                                                    ->columnSpanFull(),
                                                Checkbox::make('apprise_on_threshold_failure')
                                                    ->label(__('settings/notifications.notify_on_threshold_failures'))
                                                    ->helpertext(__('settings/notifications.notify_on_threshold_failures_helper'))
                                                    ->columnSpanFull(),
                                            ]),
                                        SimpleAlert::make('wehbook_info')
                                            ->border()
                                            ->info()
                                            ->description(__('settings/notifications.apprise_save_to_test')),
                                        Repeater::make('apprise_channel_urls')
                                            ->label(__('settings/notifications.apprise_channels'))
                                            ->schema([
                                                TextInput::make('channel_url')
                                                    ->label(__('settings/notifications.apprise_channel_url'))
                                                    ->placeholder('discord://WebhookID/WebhookToken')
                                                    ->helperText(__('settings/notifications.apprise_channel_url_helper'))
                                                    ->maxLength(2000)
                                                    ->distinct()
                                                    ->required()
                                                    ->rule(new AppriseScheme),
                                            ])
                                            ->columnSpanFull(),
                                        Actions::make([
                                            Action::make('test apprise')
                                                ->label(__('settings/notifications.test_apprise_channel'))
                                                ->action(fn (Get $get) => SendAppriseTestNotification::run(
                                                    channel_urls: $get('apprise_channel_urls'),
                                                ))
                                                ->hidden(function () {
                                                    $settings = app(NotificationSettings::class);

                                                    return empty($settings->apprise_server_url) || ! count($settings->apprise_channel_urls ?? []);
                                                }),
                                        ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
