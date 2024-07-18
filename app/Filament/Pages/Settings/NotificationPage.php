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
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Auth;

class NotificationPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Notifications';

    protected static ?string $navigationLabel = 'Notifications';

    protected static string $settings = NotificationSettings::class;

    public static function canAccess(): bool
    {
        return auth()->user()->is_admin;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->is_admin;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'md' => 3,
                ])
                    ->schema([
                        Forms\Components\Grid::make([
                            'default' => 1,
                        ])
                            ->schema([
                                Forms\Components\Section::make('Database')
                                    ->description('Notifications sent to this channel will show up under the 🔔 icon in the header.')
                                    ->schema([
                                        Forms\Components\Toggle::make('database_enabled')
                                            ->label('Enable database notifications')
                                            ->reactive()
                                            ->columnSpanFull(),
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                        ])
                                            ->hidden(fn (Forms\Get $get) => $get('database_enabled') !== true)
                                            ->schema([
                                                Forms\Components\Fieldset::make('Triggers')
                                                    ->schema([
                                                        Forms\Components\Toggle::make('database_on_speedtest_run')
                                                            ->label('Notify on every speedtest run')
                                                            ->columnSpanFull(),
                                                        Forms\Components\Toggle::make('database_on_threshold_failure')
                                                            ->label('Notify on threshold failures')
                                                            ->columnSpanFull(),
                                                    ]),
                                                Forms\Components\Actions::make([
                                                    Forms\Components\Actions\Action::make('test database')
                                                        ->label('Test database channel')
                                                        ->action(fn () => SendDatabaseTestNotification::run(user: Auth::user())),
                                                ]),
                                            ]),
                                    ])
                                    ->compact()
                                    ->columns([
                                        'default' => 1,
                                        'md' => 2,
                                    ]),

                                Forms\Components\Section::make('Pushover')
                                    ->schema([
                                        Forms\Components\Toggle::make('pushover_enabled')
                                            ->label('Enable Pushover webhook notifications')
                                            ->reactive()
                                            ->columnSpanFull(),
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                        ])
                                            ->hidden(fn (Forms\Get $get) => $get('pushover_enabled') !== true)
                                            ->schema([
                                                Forms\Components\Fieldset::make('Triggers')
                                                    ->schema([
                                                        Forms\Components\Toggle::make('pushover_on_speedtest_run')
                                                            ->label('Notify on every speedtest run')
                                                            ->columnSpanFull(),
                                                        Forms\Components\Toggle::make('pushover_on_threshold_failure')
                                                            ->label('Notify on threshold failures')
                                                            ->columnSpanFull(),
                                                    ]),
                                                Forms\Components\Repeater::make('pushover_webhooks')
                                                    ->label('Pushover Webhooks')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('url')
                                                            ->label('URL')
                                                            ->placeholder('http://api.pushover.net/1/messages.json')
                                                            ->maxLength(2000)
                                                            ->required()
                                                            ->url(),
                                                        Forms\Components\TextInput::make('user_key')
                                                            ->label('User Key')
                                                            ->placeholder('Your Pushover User Key')
                                                            ->maxLength(200)
                                                            ->required(),
                                                        Forms\Components\TextInput::make('api_token')
                                                            ->label('API Token')
                                                            ->placeholder('Your Pushover API Token')
                                                            ->maxLength(200)
                                                            ->required(),
                                                    ])
                                                    ->columnSpanFull(),
                                                Forms\Components\Actions::make([
                                                    Forms\Components\Actions\Action::make('test pushover')
                                                        ->label('Test Pushover webhook')
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

                                Forms\Components\Section::make('Discord')
                                    ->schema([
                                        Forms\Components\Toggle::make('discord_enabled')
                                            ->label('Enable Discord webhook notifications')
                                            ->reactive()
                                            ->columnSpanFull(),
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                        ])
                                            ->hidden(fn (Forms\Get $get) => $get('discord_enabled') !== true)
                                            ->schema([
                                                Forms\Components\Fieldset::make('Triggers')
                                                    ->schema([
                                                        Forms\Components\Toggle::make('discord_on_speedtest_run')
                                                            ->label('Notify on every speedtest run')
                                                            ->columnSpanFull(),
                                                        Forms\Components\Toggle::make('discord_on_threshold_failure')
                                                            ->label('Notify on threshold failures')
                                                            ->columnSpanFull(),
                                                    ]),
                                                Forms\Components\Repeater::make('discord_webhooks')
                                                    ->label('Webhooks')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('url')
                                                            ->placeholder('https://discord.com/api/webhooks/longstringofcharacters')
                                                            ->maxLength(2000)
                                                            ->required()
                                                            ->url(),
                                                    ])
                                                    ->columnSpanFull(),
                                                Forms\Components\Actions::make([
                                                    Forms\Components\Actions\Action::make('test discord')
                                                        ->label('Test Discord webhook')
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

                                Forms\Components\Section::make('Gotify')
                                    ->schema([
                                        Forms\Components\Toggle::make('gotify_enabled')
                                            ->label('Enable Gotify webhook notifications')
                                            ->reactive()
                                            ->columnSpanFull(),
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                        ])
                                            ->hidden(fn (Forms\Get $get) => $get('gotify_enabled') !== true)
                                            ->schema([
                                                Forms\Components\Fieldset::make('Triggers')
                                                    ->schema([
                                                        Forms\Components\Toggle::make('gotify_on_speedtest_run')
                                                            ->label('Notify on every speedtest run')
                                                            ->columnSpanFull(),
                                                        Forms\Components\Toggle::make('gotify_on_threshold_failure')
                                                            ->label('Notify on threshold failures')
                                                            ->columnSpanFull(),
                                                    ]),
                                                Forms\Components\Repeater::make('gotify_webhooks')
                                                    ->label('Webhooks')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('url')
                                                            ->placeholder('https://example.com/message?token=<apptoken>')
                                                            ->maxLength(2000)
                                                            ->required()
                                                            ->url(),
                                                    ])
                                                    ->columnSpanFull(),
                                                Forms\Components\Actions::make([
                                                    Forms\Components\Actions\Action::make('test gotify')
                                                        ->label('Test Gotify webhook')
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

                                Forms\Components\Section::make('Slack')
                                    ->schema([
                                        Forms\Components\Toggle::make('slack_enabled')
                                            ->label('Enable Slack webhook notifications')
                                            ->reactive()
                                            ->columnSpanFull(),
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                        ])
                                            ->hidden(fn (Forms\Get $get) => $get('slack_enabled') !== true)
                                            ->schema([
                                                Forms\Components\Fieldset::make('Triggers')
                                                    ->schema([
                                                        Forms\Components\Toggle::make('slack_on_speedtest_run')
                                                            ->label('Notify on every speedtest run')
                                                            ->columnSpanFull(),
                                                        Forms\Components\Toggle::make('slack_on_threshold_failure')
                                                            ->label('Notify on threshold failures')
                                                            ->columnSpanFull(),
                                                    ]),
                                                Forms\Components\Repeater::make('slack_webhooks')
                                                    ->label('Webhooks')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('url')
                                                            ->placeholder('https://hooks.slack.com/services/abc/xyz')
                                                            ->maxLength(2000)
                                                            ->required()
                                                            ->url(),
                                                    ])
                                                    ->columnSpanFull(),
                                                Forms\Components\Actions::make([
                                                    Forms\Components\Actions\Action::make('test Slack')
                                                        ->label('Test slack webhook')
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

                                Forms\Components\Section::make('Ntfy')
                                    ->schema([
                                        Forms\Components\Toggle::make('ntfy_enabled')
                                            ->label('Enable Ntfy webhook notifications')
                                            ->reactive()
                                            ->columnSpanFull(),
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                        ])
                                            ->hidden(fn (Forms\Get $get) => $get('ntfy_enabled') !== true)
                                            ->schema([
                                                Forms\Components\Fieldset::make('Triggers')
                                                    ->schema([
                                                        Forms\Components\Toggle::make('ntfy_on_speedtest_run')
                                                            ->label('Notify on every speedtest run')
                                                            ->columnSpanFull(),
                                                        Forms\Components\Toggle::make('ntfy_on_threshold_failure')
                                                            ->label('Notify on threshold failures')
                                                            ->columnSpanFull(),
                                                    ]),
                                                Forms\Components\Repeater::make('ntfy_webhooks')
                                                    ->label('Webhooks')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('url')
                                                            ->maxLength(2000)
                                                            ->placeholder('Your ntfy server url')
                                                            ->required()
                                                            ->url(),
                                                        Forms\Components\TextInput::make('topic')
                                                            ->label('Topic')
                                                            ->placeholder('Your ntfy Topic')
                                                            ->maxLength(200)
                                                            ->required(),
                                                        Forms\Components\TextInput::make('username')
                                                            ->label('Username')
                                                            ->placeholder('Username for Basic Auth (optional)')
                                                            ->maxLength(200),
                                                        Forms\Components\TextInput::make('password')
                                                            ->label('Password')
                                                            ->placeholder('Password for Basic Auth (optional)')
                                                            ->password()
                                                            ->maxLength(200),
                                                    ])
                                                    ->columnSpanFull(),
                                                Forms\Components\Actions::make([
                                                    Forms\Components\Actions\Action::make('test ntfy')
                                                        ->label('Test Ntfy webhook')
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

                                Forms\Components\Section::make('Mail')
                                    ->schema([
                                        Forms\Components\Toggle::make('mail_enabled')
                                            ->label('Enable mail notifications')
                                            ->reactive()
                                            ->columnSpanFull(),
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                        ])
                                            ->hidden(fn (Forms\Get $get) => $get('mail_enabled') !== true)
                                            ->schema([
                                                Forms\Components\Fieldset::make('Triggers')
                                                    ->schema([
                                                        Forms\Components\Toggle::make('mail_on_speedtest_run')
                                                            ->label('Notify on every speedtest run')
                                                            ->columnSpanFull(),
                                                        Forms\Components\Toggle::make('mail_on_threshold_failure')
                                                            ->label('Notify on threshold failures')
                                                            ->columnSpanFull(),
                                                    ]),
                                                Forms\Components\Repeater::make('mail_recipients')
                                                    ->label('Recipients')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('email_address')
                                                            ->placeholder('your@email.com')
                                                            ->email()
                                                            ->required(),
                                                    ])
                                                    ->columnSpanFull(),
                                                Forms\Components\Actions::make([
                                                    Forms\Components\Actions\Action::make('test mail')
                                                        ->label('Test mail channel')
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

                                Forms\Components\Section::make('Healthcheck.io')
                                    ->schema([
                                        Forms\Components\Toggle::make('healthcheck_enabled')
                                            ->label('Enable healthcheck.io webhook notifications')
                                            ->reactive()
                                            ->columnSpanFull(),
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                        ])
                                            ->hidden(fn (Forms\Get $get) => $get('healthcheck_enabled') !== true)
                                            ->schema([
                                                Forms\Components\Fieldset::make('Triggers')
                                                    ->schema([
                                                        Forms\Components\Toggle::make('healthcheck_on_speedtest_run')
                                                            ->label('Notify on every speedtest run')
                                                            ->columnSpanFull(),
                                                        Forms\Components\Toggle::make('healthcheck_on_threshold_failure')
                                                            ->label('Notify on threshold failures')
                                                            ->helperText('Threshold notifications will be sent to the /fail path of the URL.')
                                                            ->columnSpanFull(),
                                                    ]),
                                                Forms\Components\Repeater::make('healthcheck_webhooks')
                                                    ->label('webhooks')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('url')
                                                            ->placeholder('https://hc-ping.com/your-uuid-here')
                                                            ->maxLength(2000)
                                                            ->required()
                                                            ->url(),
                                                    ])
                                                    ->columnSpanFull(),
                                                Forms\Components\Actions::make([
                                                    Forms\Components\Actions\Action::make('test healthcheck')
                                                        ->label('Test healthcheck.io webhook')
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

                                Forms\Components\Section::make('Telegram')
                                    ->schema([
                                        Forms\Components\Toggle::make('telegram_enabled')
                                            ->label('Enable telegram notifications')
                                            ->reactive()
                                            ->columnSpanFull(),
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                        ])
                                            ->hidden(fn (Forms\Get $get) => $get('telegram_enabled') !== true)
                                            ->schema([
                                                Forms\Components\Fieldset::make('Options')
                                                    ->schema([
                                                        Forms\Components\Toggle::make('telegram_disable_notification')
                                                            ->label('Send the message silently to the user')
                                                            ->columnSpanFull(),
                                                    ]),
                                                Forms\Components\Fieldset::make('Triggers')
                                                    ->schema([
                                                        Forms\Components\Toggle::make('telegram_on_speedtest_run')
                                                            ->label('Notify on every speedtest run')
                                                            ->columnSpanFull(),
                                                        Forms\Components\Toggle::make('telegram_on_threshold_failure')
                                                            ->label('Notify on threshold failures')
                                                            ->columnSpanFull(),
                                                    ]),
                                                Forms\Components\Repeater::make('telegram_recipients')
                                                    ->label('Recipients')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('telegram_chat_id')
                                                            ->placeholder('12345678910')
                                                            ->label('Telegram Chat ID')
                                                            ->maxLength(50)
                                                            ->required(),
                                                    ])
                                                    ->columnSpanFull(),
                                                Forms\Components\Actions::make([
                                                    Forms\Components\Actions\Action::make('test telegram')
                                                        ->label('Test Telegram channel')
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

                                Forms\Components\Section::make('Webhook')
                                    ->schema([
                                        Forms\Components\Toggle::make('webhook_enabled')
                                            ->label('Enable webhook notifications')
                                            ->reactive()
                                            ->columnSpanFull(),
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                        ])
                                            ->hidden(fn (Forms\Get $get) => $get('webhook_enabled') !== true)
                                            ->schema([
                                                Forms\Components\Fieldset::make('Triggers')
                                                    ->schema([
                                                        Forms\Components\Toggle::make('webhook_on_speedtest_run')
                                                            ->label('Notify on every speedtest run')
                                                            ->columnSpan(2),
                                                        Forms\Components\Toggle::make('webhook_on_threshold_failure')
                                                            ->label('Notify on threshold failures')
                                                            ->columnSpan(2),
                                                    ]),
                                                Forms\Components\Repeater::make('webhook_urls')
                                                    ->label('Recipients')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('url')
                                                            ->placeholder('https://webhook.site/longstringofcharacters')
                                                            ->maxLength(2000)
                                                            ->required()
                                                            ->url(),
                                                    ])
                                                    ->columnSpanFull(),
                                                Forms\Components\Actions::make([
                                                    Forms\Components\Actions\Action::make('test webhook')
                                                        ->label('Test webhook channel')
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
                            ])
                            ->columnSpan([
                                'md' => 2,
                            ]),

                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\View::make('filament.forms.notifications-helptext'),
                            ])
                            ->columnSpan([
                                'md' => 1,
                            ]),
                    ]),
            ]);
    }
}
