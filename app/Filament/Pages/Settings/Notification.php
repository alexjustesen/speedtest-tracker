<?php

namespace App\Filament\Pages\Settings;

use App\Actions\Notifications\SendAppriseTestNotification;
use App\Actions\Notifications\SendDatabaseTestNotification;
use App\Actions\Notifications\SendDiscordTestNotification;
use App\Actions\Notifications\SendGotifyTestNotification;
use App\Actions\Notifications\SendHealthCheckTestNotification;
use App\Actions\Notifications\SendMailTestNotification;
use App\Actions\Notifications\SendNtfyTestNotification;
use App\Actions\Notifications\SendPushoverTestNotification;
use App\Actions\Notifications\SendSlackTestNotification;
use App\Actions\Notifications\SendTelegramTestNotification;
use App\Actions\Notifications\SendWebhookTestNotification;
use App\Rules\AppriseScheme;
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
use Filament\Schemas\Components\Section;
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
                                                    ->label(__('settings/notifications.notify_on_every_speedtest_run')),

                                                Checkbox::make('database_on_threshold_failure')
                                                    ->label(__('settings/notifications.notify_on_threshold_failures')),
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
                                                    ->label(__('settings/notifications.notify_on_every_speedtest_run')),

                                                Checkbox::make('mail_on_threshold_failure')
                                                    ->label(__('settings/notifications.notify_on_threshold_failures')),
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
                                                    ->label(__('settings/notifications.notify_on_every_speedtest_run')),

                                                Checkbox::make('webhook_on_threshold_failure')
                                                    ->label(__('settings/notifications.notify_on_threshold_failures')),
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
                                                    ->placeholder('http://localhost:8000')
                                                    ->maxLength(2000)
                                                    ->required()
                                                    ->url()
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
                                                    ->columnSpanFull(),
                                                Checkbox::make('apprise_on_threshold_failure')
                                                    ->label(__('settings/notifications.notify_on_threshold_failures'))
                                                    ->columnSpanFull(),
                                            ]),
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
                                                ->hidden(fn (Get $get) => ! count($get('apprise_channel_urls'))),
                                        ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),

                // ! DEPRECATED CHANNELS
                SimpleAlert::make('deprecation_warning')
                    ->title('Deprecated Notification Channels')
                    ->description('The following notification channels are deprecated and will be removed in a future release!')
                    ->border()
                    ->warning()
                    ->columnSpanFull(),

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
                                Section::make('Pushover')
                                    ->description('⚠️ Pushover is deprecated and will be removed in a future release.')
                                    ->schema([
                                        Toggle::make('pushover_enabled')
                                            ->label('Enable Pushover webhook notifications')
                                            ->reactive()
                                            ->columnSpanFull(),
                                        Grid::make([
                                            'default' => 1,
                                        ])
                                            ->hidden(fn (Get $get) => $get('pushover_enabled') !== true)
                                            ->schema([
                                                Fieldset::make('Triggers')
                                                    ->schema([
                                                        Toggle::make('pushover_on_speedtest_run')
                                                            ->label('Notify on every speedtest run')
                                                            ->columnSpanFull(),
                                                        Toggle::make('pushover_on_threshold_failure')
                                                            ->label('Notify on threshold failures')
                                                            ->columnSpanFull(),
                                                    ]),
                                                Repeater::make('pushover_webhooks')
                                                    ->label('Pushover Webhooks')
                                                    ->schema([
                                                        TextInput::make('url')
                                                            ->label('URL')
                                                            ->placeholder('http://api.pushover.net/1/messages.json')
                                                            ->maxLength(2000)
                                                            ->required()
                                                            ->url(),
                                                        TextInput::make('user_key')
                                                            ->label('User Key')
                                                            ->placeholder('Your Pushover User Key')
                                                            ->maxLength(200)
                                                            ->required(),
                                                        TextInput::make('api_token')
                                                            ->label('API Token')
                                                            ->placeholder('Your Pushover API Token')
                                                            ->maxLength(200)
                                                            ->required(),
                                                    ])
                                                    ->columnSpanFull(),
                                                Actions::make([
                                                    Action::make('test pushover')
                                                        ->label('Test Pushover webhook')
                                                        ->action(fn (Get $get) => SendPushoverTestNotification::run(
                                                            webhooks: $get('pushover_webhooks')
                                                        ))
                                                        ->hidden(fn (Get $get) => ! count($get('pushover_webhooks'))),
                                                ]),
                                            ]),
                                    ])
                                    ->compact()
                                    ->columnSpan('full'),

                                Section::make('Discord')
                                    ->description('⚠️ Discord is deprecated and will be removed in a future release.')
                                    ->schema([
                                        Toggle::make('discord_enabled')
                                            ->label('Enable Discord webhook notifications')
                                            ->reactive()
                                            ->columnSpanFull(),
                                        Grid::make([
                                            'default' => 1,
                                        ])
                                            ->hidden(fn (Get $get) => $get('discord_enabled') !== true)
                                            ->schema([
                                                Fieldset::make('Triggers')
                                                    ->schema([
                                                        Toggle::make('discord_on_speedtest_run')
                                                            ->label('Notify on every speedtest run')
                                                            ->columnSpanFull(),
                                                        Toggle::make('discord_on_threshold_failure')
                                                            ->label('Notify on threshold failures')
                                                            ->columnSpanFull(),
                                                    ]),
                                                Repeater::make('discord_webhooks')
                                                    ->label('Webhooks')
                                                    ->schema([
                                                        TextInput::make('url')
                                                            ->placeholder('https://discord.com/api/webhooks/longstringofcharacters')
                                                            ->maxLength(2000)
                                                            ->required()
                                                            ->url(),
                                                    ])
                                                    ->columnSpanFull(),
                                                Actions::make([
                                                    Action::make('test discord')
                                                        ->label('Test Discord webhook')
                                                        ->action(fn (Get $get) => SendDiscordTestNotification::run(webhooks: $get('discord_webhooks')))
                                                        ->hidden(fn (Get $get) => ! count($get('discord_webhooks'))),
                                                ]),
                                            ]),
                                    ])
                                    ->compact()
                                    ->columnSpan('full'),

                                Section::make('Gotify')
                                    ->description('⚠️ Gotify is deprecated and will be removed in a future release.')
                                    ->schema([
                                        Toggle::make('gotify_enabled')
                                            ->label('Enable Gotify webhook notifications')
                                            ->reactive()
                                            ->columnSpanFull(),
                                        Grid::make([
                                            'default' => 1,
                                        ])
                                            ->hidden(fn (Get $get) => $get('gotify_enabled') !== true)
                                            ->schema([
                                                Fieldset::make('Triggers')
                                                    ->schema([
                                                        Toggle::make('gotify_on_speedtest_run')
                                                            ->label('Notify on every speedtest run')
                                                            ->columnSpanFull(),
                                                        Toggle::make('gotify_on_threshold_failure')
                                                            ->label('Notify on threshold failures')
                                                            ->columnSpanFull(),
                                                    ]),
                                                Repeater::make('gotify_webhooks')
                                                    ->label('Webhooks')
                                                    ->schema([
                                                        TextInput::make('url')
                                                            ->placeholder('https://example.com/message?token=<apptoken>')
                                                            ->maxLength(2000)
                                                            ->required()
                                                            ->url(),
                                                    ])
                                                    ->columnSpanFull(),
                                                Actions::make([
                                                    Action::make('test gotify')
                                                        ->label('Test Gotify webhook')
                                                        ->action(fn (Get $get) => SendgotifyTestNotification::run(webhooks: $get('gotify_webhooks')))
                                                        ->hidden(fn (Get $get) => ! count($get('gotify_webhooks'))),
                                                ]),
                                            ]),
                                    ])
                                    ->compact()
                                    ->columnSpan('full'),

                                Section::make('Slack')
                                    ->description('⚠️ Slack is deprecated and will be removed in a future release.')
                                    ->schema([
                                        Toggle::make('slack_enabled')
                                            ->label('Enable Slack webhook notifications')
                                            ->reactive()
                                            ->columnSpanFull(),
                                        Grid::make([
                                            'default' => 1,
                                        ])
                                            ->hidden(fn (Get $get) => $get('slack_enabled') !== true)
                                            ->schema([
                                                Fieldset::make('Triggers')
                                                    ->schema([
                                                        Toggle::make('slack_on_speedtest_run')
                                                            ->label('Notify on every speedtest run')
                                                            ->columnSpanFull(),
                                                        Toggle::make('slack_on_threshold_failure')
                                                            ->label('Notify on threshold failures')
                                                            ->columnSpanFull(),
                                                    ]),
                                                Repeater::make('slack_webhooks')
                                                    ->label('Webhooks')
                                                    ->schema([
                                                        TextInput::make('url')
                                                            ->placeholder('https://hooks.slack.com/services/abc/xyz')
                                                            ->maxLength(2000)
                                                            ->required()
                                                            ->url(),
                                                    ])
                                                    ->columnSpanFull(),
                                                Actions::make([
                                                    Action::make('test Slack')
                                                        ->label('Test slack webhook')
                                                        ->action(fn (Get $get) => SendSlackTestNotification::run(webhooks: $get('slack_webhooks')))
                                                        ->hidden(fn (Get $get) => ! count($get('slack_webhooks'))),
                                                ]),
                                            ]),
                                    ])
                                    ->compact()
                                    ->columnSpan('full'),

                                Section::make('Ntfy')
                                    ->description('⚠️ Ntfy is deprecated and will be removed in a future release.')
                                    ->schema([
                                        Toggle::make('ntfy_enabled')
                                            ->label('Enable Ntfy webhook notifications')
                                            ->reactive()
                                            ->columnSpanFull(),
                                        Grid::make([
                                            'default' => 1,
                                        ])
                                            ->hidden(fn (Get $get) => $get('ntfy_enabled') !== true)
                                            ->schema([
                                                Fieldset::make('Triggers')
                                                    ->schema([
                                                        Toggle::make('ntfy_on_speedtest_run')
                                                            ->label('Notify on every speedtest run')
                                                            ->columnSpanFull(),
                                                        Toggle::make('ntfy_on_threshold_failure')
                                                            ->label('Notify on threshold failures')
                                                            ->columnSpanFull(),
                                                    ]),
                                                Repeater::make('ntfy_webhooks')
                                                    ->label('Webhooks')
                                                    ->schema([
                                                        TextInput::make('url')
                                                            ->maxLength(2000)
                                                            ->placeholder('Your ntfy server url')
                                                            ->required()
                                                            ->url(),
                                                        TextInput::make('topic')
                                                            ->label('Topic')
                                                            ->placeholder('Your ntfy Topic')
                                                            ->maxLength(200)
                                                            ->required(),
                                                        TextInput::make('username')
                                                            ->label('Username')
                                                            ->placeholder('Username for Basic Auth (optional)')
                                                            ->maxLength(200),
                                                        TextInput::make('password')
                                                            ->label('Password')
                                                            ->placeholder('Password for Basic Auth (optional)')
                                                            ->password()
                                                            ->maxLength(200),
                                                    ])
                                                    ->columnSpanFull(),
                                                Actions::make([
                                                    Action::make('test ntfy')
                                                        ->label('Test Ntfy webhook')
                                                        ->action(fn (Get $get) => SendNtfyTestNotification::run(webhooks: $get('ntfy_webhooks')))
                                                        ->hidden(fn (Get $get) => ! count($get('ntfy_webhooks'))),
                                                ]),
                                            ]),
                                    ])
                                    ->compact()
                                    ->columnSpan('full'),

                                Section::make('Healthcheck.io')
                                    ->description('⚠️ Healthcheck.io is deprecated and will be removed in a future release.')
                                    ->schema([
                                        Toggle::make('healthcheck_enabled')
                                            ->label('Enable healthcheck.io webhook notifications')
                                            ->reactive()
                                            ->columnSpanFull(),
                                        Grid::make([
                                            'default' => 1,
                                        ])
                                            ->hidden(fn (Get $get) => $get('healthcheck_enabled') !== true)
                                            ->schema([
                                                Fieldset::make('Triggers')
                                                    ->schema([
                                                        Toggle::make('healthcheck_on_speedtest_run')
                                                            ->label('Notify on every speedtest run')
                                                            ->columnSpanFull(),
                                                        Toggle::make('healthcheck_on_threshold_failure')
                                                            ->label('Notify on threshold failures')
                                                            ->helperText('Threshold notifications will be sent to the /fail path of the URL.')
                                                            ->columnSpanFull(),
                                                    ]),
                                                Repeater::make('healthcheck_webhooks')
                                                    ->label('webhooks')
                                                    ->schema([
                                                        TextInput::make('url')
                                                            ->placeholder('https://hc-ping.com/your-uuid-here')
                                                            ->maxLength(2000)
                                                            ->required()
                                                            ->url(),
                                                    ])
                                                    ->columnSpanFull(),
                                                Actions::make([
                                                    Action::make('test healthcheck')
                                                        ->label('Test healthcheck.io webhook')
                                                        ->action(fn (Get $get) => SendHealthCheckTestNotification::run(webhooks: $get('healthcheck_webhooks')))
                                                        ->hidden(fn (Get $get) => ! count($get('healthcheck_webhooks'))),
                                                ]),
                                            ]),
                                    ])
                                    ->compact()
                                    ->columnSpan('full'),

                                Section::make('Telegram')
                                    ->description('⚠️ Telegram is deprecated and will be removed in a future release.')
                                    ->schema([
                                        Toggle::make('telegram_enabled')
                                            ->label('Enable telegram notifications')
                                            ->reactive()
                                            ->columnSpanFull(),
                                        Grid::make([
                                            'default' => 1,
                                        ])
                                            ->hidden(fn (Get $get) => $get('telegram_enabled') !== true)
                                            ->schema([
                                                Fieldset::make('Options')
                                                    ->schema([
                                                        Toggle::make('telegram_disable_notification')
                                                            ->label('Send the message silently to the user')
                                                            ->columnSpanFull(),
                                                    ]),
                                                Fieldset::make('Triggers')
                                                    ->schema([
                                                        Toggle::make('telegram_on_speedtest_run')
                                                            ->label('Notify on every speedtest run')
                                                            ->columnSpanFull(),
                                                        Toggle::make('telegram_on_threshold_failure')
                                                            ->label('Notify on threshold failures')
                                                            ->columnSpanFull(),
                                                    ]),
                                                Repeater::make('telegram_recipients')
                                                    ->label('Recipients')
                                                    ->schema([
                                                        TextInput::make('telegram_chat_id')
                                                            ->placeholder('12345678910')
                                                            ->label('Telegram Chat ID')
                                                            ->maxLength(50)
                                                            ->required(),
                                                    ])
                                                    ->columnSpanFull(),
                                                Actions::make([
                                                    Action::make('test telegram')
                                                        ->label('Test Telegram channel')
                                                        ->action(fn (Get $get) => SendTelegramTestNotification::run(recipients: $get('telegram_recipients')))
                                                        ->hidden(fn (Get $get) => ! count($get('telegram_recipients')) || blank(config('telegram.bot'))),
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
