<?php

namespace App\Filament\Pages\Settings;

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
use App\Settings\NotificationSettings;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;

class NotificationPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static ?int $navigationSort = 3;

    protected static string $settings = NotificationSettings::class;

    public static function getNavigationGroup(): string
    {
        return __('translations.settings');
    }

    public function getTitle(): string
    {
        return __('translations.notifications.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('translations.notifications.label');
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
                Section::make(__('translations.database'))
                    ->description(__('translations.database_description'))
                    ->schema([
                        Toggle::make('database_enabled')
                            ->label(__('translations.enable_database_notifications'))
                            ->reactive()
                            ->columnSpanFull(),
                        Grid::make([
                            'default' => 1,
                        ])
                            ->hidden(fn (Forms\Get $get) => $get('database_enabled') !== true)
                            ->schema([
                                Fieldset::make('Triggers')
                                    ->label(__('translations.triggers'))
                                    ->schema([
                                        Toggle::make('database_on_speedtest_run')
                                            ->label(__('translations.notify_on_every_speedtest_run'))
                                            ->columnSpanFull(),
                                        Toggle::make('database_on_threshold_failure')
                                            ->label(__('translations.notify_on_threshold_failures'))
                                            ->columnSpanFull(),
                                    ]),
                                Actions::make([
                                    Action::make('test database')
                                        ->label(__('translations.test_database_channel'))
                                        ->action(fn () => SendDatabaseTestNotification::run(user: Auth::user())),
                                ]),
                            ]),
                    ])
                    ->compact()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),

                Section::make(__('translations.mail'))
                    ->schema([
                        Toggle::make('mail_enabled')
                            ->label(__('translations.enable_mail_notifications'))
                            ->reactive()
                            ->columnSpanFull(),
                        Grid::make([
                            'default' => 1,
                        ])
                            ->hidden(fn (Forms\Get $get) => $get('mail_enabled') !== true)
                            ->schema([
                                Fieldset::make('Triggers')
                                    ->label(__('translations.triggers'))
                                    ->schema([
                                        Toggle::make('mail_on_speedtest_run')
                                            ->label(__('translations.notify_on_every_speedtest_run'))
                                            ->columnSpanFull(),
                                        Toggle::make('mail_on_threshold_failure')
                                            ->label(__('translations.notify_on_threshold_failures'))
                                            ->columnSpanFull(),
                                    ]),
                                Repeater::make('mail_recipients')
                                    ->label(__('translations.recipients'))
                                    ->schema([
                                        Forms\Components\TextInput::make('email_address')
                                            ->label(__('translations.email_address'))
                                            ->placeholder('your@email.com')
                                            ->email()
                                            ->required(),
                                    ])
                                    ->columnSpanFull(),
                                Actions::make([
                                    Action::make('test mail')
                                        ->label(__('translations.test_mail_channel'))
                                        ->action(fn (Forms\Get $get) => SendMailTestNotification::run(recipients: $get('mail_recipients')))
                                        ->hidden(fn (Forms\Get $get) => ! count($get('mail_recipients'))),
                                ]),
                            ]),
                    ])
                    ->compact()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),

                Section::make(__('translations.webhook'))
                    ->schema([
                        Toggle::make('webhook_enabled')
                            ->label(__('translations.enable_webhook_notifications'))
                            ->reactive()
                            ->columnSpanFull(),
                        Grid::make([
                            'default' => 1,
                        ])
                            ->hidden(fn (Forms\Get $get) => $get('webhook_enabled') !== true)
                            ->schema([
                                Fieldset::make('Triggers')
                                    ->label('translations.triggers')
                                    ->schema([
                                        Toggle::make('webhook_on_speedtest_run')
                                            ->label(__('notify_on_every_speedtest_run'))
                                            ->columnSpan(2),
                                        Toggle::make('webhook_on_threshold_failure')
                                            ->label(__('notify_on_threshold_failures'))
                                            ->columnSpan(2),
                                    ]),
                                Repeater::make('webhook_urls')
                                    ->label(__('translations.recipients'))
                                    ->schema([
                                        Forms\Components\TextInput::make('url')
                                            ->label(__('translations.url'))
                                            ->placeholder('https://webhook.site/longstringofcharacters')
                                            ->maxLength(2000)
                                            ->required()
                                            ->url(),
                                    ])
                                    ->columnSpanFull(),
                                Actions::make([
                                    Action::make('test webhook')
                                        ->label(__('translations.test_webhook_channel'))
                                        ->action(fn (Forms\Get $get) => SendWebhookTestNotification::run(webhooks: $get('webhook_urls')))
                                        ->hidden(fn (Forms\Get $get) => ! count($get('webhook_urls'))),
                                ]),
                            ]),
                    ])
                    ->compact()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),

                Section::make(__('translations.pushover'))
                    ->schema([
                        Toggle::make('pushover_enabled')
                            ->label(__('translations.enable_pushover_webhook_notifications'))
                            ->reactive()
                            ->columnSpanFull(),
                        Grid::make([
                            'default' => 1,
                        ])
                            ->hidden(fn (Forms\Get $get) => $get('pushover_enabled') !== true)
                            ->schema([
                                Fieldset::make('Triggers')
                                    ->label(__('translations.triggers'))
                                    ->schema([
                                        Toggle::make('pushover_on_speedtest_run')
                                            ->label(__('translations.notify_on_every_speedtest_run'))
                                            ->columnSpanFull(),
                                        Toggle::make('pushover_on_threshold_failure')
                                            ->label(__('translations.notify_on_threshold_failures'))
                                            ->columnSpanFull(),
                                    ]),
                                Repeater::make('pushover_webhooks')
                                    ->label(__('translations.pushover_webhooks'))
                                    ->schema([
                                        Forms\Components\TextInput::make('url')
                                            ->label(__('translations.url'))
                                            ->placeholder('http://api.pushover.net/1/messages.json')
                                            ->maxLength(2000)
                                            ->required()
                                            ->url(),
                                        Forms\Components\TextInput::make('user_key')
                                            ->label(__('translations.user_key'))
                                            ->placeholder(__('translations.your_pushover_user_key'))
                                            ->maxLength(200)
                                            ->required(),
                                        Forms\Components\TextInput::make('api_token')
                                            ->label(__('translations.api_token'))
                                            ->placeholder(__('translations.your_pushover_api_token'))
                                            ->maxLength(200)
                                            ->required(),
                                    ])
                                    ->columnSpanFull(),
                                Actions::make([
                                    Action::make('test pushover')
                                        ->label(__('translations.test_pushover_webhook'))
                                        ->action(fn (Forms\Get $get) => SendPushoverTestNotification::run(
                                            webhooks: $get('pushover_webhooks')
                                        ))
                                        ->hidden(fn (Forms\Get $get) => ! count($get('pushover_webhooks'))),
                                ]),
                            ]),
                    ])
                    ->compact()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),

                Section::make(__('translations.discord'))
                    ->schema([
                        Toggle::make('discord_enabled')
                            ->label(__('translations.enable_discord_webhook_notifications'))
                            ->reactive()
                            ->columnSpanFull(),
                        Grid::make([
                            'default' => 1,
                        ])
                            ->hidden(fn (Forms\Get $get) => $get('discord_enabled') !== true)
                            ->schema([
                                Fieldset::make('Triggers')
                                    ->label(__('translations.triggers'))
                                    ->schema([
                                        Toggle::make('discord_on_speedtest_run')
                                            ->label(__('translations.notify_on_every_speedtest_run'))
                                            ->columnSpanFull(),
                                        Toggle::make('discord_on_threshold_failure')
                                            ->label(__('translations.notify_on_threshold_failures'))
                                            ->columnSpanFull(),
                                    ]),
                                Repeater::make('discord_webhooks')
                                    ->label(__('translations.webhooks'))
                                    ->schema([
                                        Forms\Components\TextInput::make('url')
                                            ->label(__('translations.url'))
                                            ->placeholder('https://discord.com/api/webhooks/longstringofcharacters')
                                            ->maxLength(2000)
                                            ->required()
                                            ->url(),
                                    ])
                                    ->columnSpanFull(),
                                Actions::make([
                                    Action::make('test discord')
                                        ->label(__('translations.test_discord_webhook'))
                                        ->action(fn (Forms\Get $get) => SendDiscordTestNotification::run(webhooks: $get('discord_webhooks')))
                                        ->hidden(fn (Forms\Get $get) => ! count($get('discord_webhooks'))),
                                ]),
                            ]),
                    ])
                    ->compact()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),

                Section::make(__('translations.gotify'))
                    ->schema([
                        Toggle::make('gotify_enabled')
                            ->label(__('translations.gotify_enabled'))
                            ->reactive()
                            ->columnSpanFull(),
                        Grid::make([
                            'default' => 1,
                        ])
                            ->hidden(fn (Forms\Get $get) => $get('gotify_enabled') !== true)
                            ->schema([
                                Fieldset::make('Triggers')
                                    ->schema([
                                        Toggle::make('gotify_on_speedtest_run')
                                            ->label(__('translations.notify_on_every_speedtest_run'))
                                            ->columnSpanFull(),
                                        Toggle::make('gotify_on_threshold_failure')
                                            ->label(__('translations.notify_on_threshold_failures'))
                                            ->columnSpanFull(),
                                    ]),
                                Repeater::make('gotify_webhooks')
                                    ->label(__('translations.webhooks'))
                                    ->schema([
                                        Forms\Components\TextInput::make('url')
                                            ->label(__('translations.url'))
                                            ->placeholder('https://example.com/message?token=<apptoken>')
                                            ->maxLength(2000)
                                            ->required()
                                            ->url(),
                                    ])
                                    ->columnSpanFull(),
                                Actions::make([
                                    Action::make('test gotify')
                                        ->label(__('translations.test_gotify_webhook'))
                                        ->action(fn (Forms\Get $get) => SendgotifyTestNotification::run(webhooks: $get('gotify_webhooks')))
                                        ->hidden(fn (Forms\Get $get) => ! count($get('gotify_webhooks'))),
                                ]),
                            ]),
                    ])
                    ->compact()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),

                Section::make(__('translations.slack'))
                    ->schema([
                        Toggle::make('slack_enabled')
                            ->label(__('translations.slack_enabled'))
                            ->reactive()
                            ->columnSpanFull(),
                        Grid::make([
                            'default' => 1,
                        ])
                            ->hidden(fn (Forms\Get $get) => $get('slack_enabled') !== true)
                            ->schema([
                                Fieldset::make('Triggers')
                                    ->label('translations.triggers')
                                    ->schema([
                                        Toggle::make('slack_on_speedtest_run')
                                            ->label(__('translations.notify_on_every_speedtest_run'))
                                            ->columnSpanFull(),
                                        Toggle::make('slack_on_threshold_failure')
                                            ->label(__('translations.notify_on_threshold_failures'))
                                            ->columnSpanFull(),
                                    ]),
                                Repeater::make('slack_webhooks')
                                    ->label(__('translations.webhooks'))
                                    ->schema([
                                        Forms\Components\TextInput::make('url')
                                            ->label(__('translations.url'))
                                            ->placeholder('https://hooks.slack.com/services/abc/xyz')
                                            ->maxLength(2000)
                                            ->required()
                                            ->url(),
                                    ])
                                    ->columnSpanFull(),
                                Actions::make([
                                    Action::make('test Slack')
                                        ->label(__('translations.test_slack_webhook'))
                                        ->action(fn (Forms\Get $get) => SendSlackTestNotification::run(webhooks: $get('slack_webhooks')))
                                        ->hidden(fn (Forms\Get $get) => ! count($get('slack_webhooks'))),
                                ]),
                            ]),
                    ])
                    ->compact()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),

                Section::make(__('translations.ntfy'))
                    ->schema([
                        Toggle::make('ntfy_enabled')
                            ->label(__('translations.ntfy_enabled'))
                            ->reactive()
                            ->columnSpanFull(),
                        Grid::make([
                            'default' => 1,
                        ])
                            ->hidden(fn (Forms\Get $get) => $get('ntfy_enabled') !== true)
                            ->schema([
                                Fieldset::make('Triggers')
                                    ->label('translations.triggers')
                                    ->schema([
                                        Toggle::make('ntfy_on_speedtest_run')
                                            ->label(__('translations.notify_on_every_speedtest_run'))
                                            ->columnSpanFull(),
                                        Toggle::make('ntfy_on_threshold_failure')
                                            ->label(__('translations.notify_on_threshold_failures'))
                                            ->columnSpanFull(),
                                    ]),
                                Repeater::make('ntfy_webhooks')
                                    ->label(__('translations.webhooks'))
                                    ->schema([
                                        Forms\Components\TextInput::make('url')
                                            ->label(__('translations.url'))
                                            ->maxLength(2000)
                                            ->placeholder(__('translations.your_ntfy_server_url'))
                                            ->required()
                                            ->url(),
                                        Forms\Components\TextInput::make('topic')
                                            ->label(__('translations.topic'))
                                            ->placeholder(__('translations.your_ntfy_topic'))
                                            ->maxLength(200)
                                            ->required(),
                                        Forms\Components\TextInput::make('username')
                                            ->label(__('translations.username'))
                                            ->placeholder(__('translations.username_placeholder'))
                                            ->maxLength(200),
                                        Forms\Components\TextInput::make('password')
                                            ->label(__('translations.password'))
                                            ->placeholder(__('translations.password_placeholder'))
                                            ->password()
                                            ->maxLength(200),
                                    ])
                                    ->columnSpanFull(),
                                Actions::make([
                                    Action::make('test ntfy')
                                        ->label(__('translations.test_ntfy_webhook'))
                                        ->action(fn (Forms\Get $get) => SendNtfyTestNotification::run(webhooks: $get('ntfy_webhooks')))
                                        ->hidden(fn (Forms\Get $get) => ! count($get('ntfy_webhooks'))),
                                ]),
                            ]),
                    ])
                    ->compact()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),

                Section::make(__('translations.healthcheck_io'))
                    ->schema([
                        Toggle::make('healthcheck_enabled')
                            ->label(__('translations.healthcheck_enabled'))
                            ->reactive()
                            ->columnSpanFull(),
                        Grid::make([
                            'default' => 1,
                        ])
                            ->hidden(fn (Forms\Get $get) => $get('healthcheck_enabled') !== true)
                            ->schema([
                                Fieldset::make('Triggers')
                                    ->label('translations.triggers')
                                    ->schema([
                                        Toggle::make('healthcheck_on_speedtest_run')
                                            ->label(__('translations.notify_on_every_speedtest_run'))
                                            ->columnSpanFull(),
                                        Toggle::make('healthcheck_on_threshold_failure')
                                            ->label(__('translations.notify_on_threshold_failures'))
                                            ->helperText(__('translations.threshold_helper_text'))
                                            ->columnSpanFull(),
                                    ]),
                                Repeater::make('healthcheck_webhooks')
                                    ->label(__('translations.webhooks'))
                                    ->schema([
                                        Forms\Components\TextInput::make('url')
                                            ->label(__('translations.url'))
                                            ->placeholder('https://hc-ping.com/your-uuid-here')
                                            ->maxLength(2000)
                                            ->required()
                                            ->url(),
                                    ])
                                    ->columnSpanFull(),
                                Actions::make([
                                    Action::make('test healthcheck')
                                        ->label(__('translations.test_healthcheck_webhook'))
                                        ->action(fn (Forms\Get $get) => SendHealthCheckTestNotification::run(webhooks: $get('healthcheck_webhooks')))
                                        ->hidden(fn (Forms\Get $get) => ! count($get('healthcheck_webhooks'))),
                                ]),
                            ]),
                    ])
                    ->compact()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),

                Section::make(__('translations.telegram'))
                    ->schema([
                        Toggle::make('telegram_enabled')
                            ->label(__('translations.enable_telegram'))
                            ->reactive()
                            ->columnSpanFull(),
                        Grid::make([
                            'default' => 1,
                        ])
                            ->hidden(fn (Forms\Get $get) => $get('telegram_enabled') !== true)
                            ->schema([
                                Fieldset::make('Options')
                                    ->label(__('translations.options'))
                                    ->schema([
                                        Toggle::make('telegram_disable_notification')
                                            ->label(__('translations.telegram_disable_notification'))
                                            ->columnSpanFull(),
                                    ]),
                                Fieldset::make('Triggers')
                                    ->label(__('translations.triggers'))
                                    ->schema([
                                        Toggle::make('telegram_on_speedtest_run')
                                            ->label(__('translations.notify_on_every_speedtest_run'))
                                            ->columnSpanFull(),
                                        Toggle::make('telegram_on_threshold_failure')
                                            ->label(__('translations.notify_on_threshold_failures'))
                                            ->columnSpanFull(),
                                    ]),
                                Repeater::make('telegram_recipients')
                                    ->label(__('translations.recipients'))
                                    ->schema([
                                        Forms\Components\TextInput::make('telegram_chat_id')
                                            ->placeholder('12345678910')
                                            ->label('')
                                            ->maxLength(50)
                                            ->required(),
                                    ])
                                    ->columnSpanFull(),
                                Actions::make([
                                    Action::make('test telegram')
                                        ->label(__('translations.test_telegram_webhook'))
                                        ->action(fn (Forms\Get $get) => SendTelegramTestNotification::run(recipients: $get('telegram_recipients')))
                                        ->hidden(fn (Forms\Get $get) => ! count($get('telegram_recipients')) || blank(config('telegram.bot'))),
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
