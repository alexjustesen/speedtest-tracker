<?php

namespace App\Filament\Pages\Settings;

use App\Actions\Notifications\SendDatabaseTestNotification;
use App\Actions\Notifications\SendMailTestNotification;
use App\Actions\Notifications\SendTelegramTestNotification;
use App\Settings\NotificationSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Auth;
use Spatie\WebhookServer\WebhookCall;

class NotificationPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Notifications';

    protected static ?string $navigationLabel = 'Notifications';

    protected static string $settings = NotificationSettings::class;

    public function mount(): void
    {
        parent::mount();

        abort_unless(auth()->user()->is_admin, 403);
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
                                            'default' => 1, ])
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
                                            ]),
                                        Forms\Components\Repeater::make('webhook_urls')
                                            ->label('Recipients')
                                            ->schema([
                                                Forms\Components\TextInput::make('url')
                                                    ->maxLength(2000)
                                                    ->required()
                                                    ->url()
                                                    ->columnSpanFull(),
                                            ])
                                            ->hidden(fn (Forms\Get $get) => $get('webhook_enabled') !== true)
                                            ->columnSpanFull(),
                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('test webhook')
                                                ->label('Test webhook channel')
                                                ->action(function (): void {
                                                    $notificationSettings = new (NotificationSettings::class);

                                                    if (blank($notificationSettings->webhook_urls)) {
                                                        Notification::make()
                                                            ->title('You need to add webhook urls.')
                                                            ->body('Make sure to click "Save changes" before testing webhook notifications.')
                                                            ->warning()
                                                            ->send();

                                                        return;
                                                    }

                                                    foreach ($notificationSettings->webhook_urls as $url) {
                                                        WebhookCall::create()
                                                            ->url($url['url'])
                                                            ->payload(['message' => '👋 Testing the Webhook notification channel.'])
                                                            ->doNotSign()
                                                            ->dispatch();
                                                    }

                                                    Notification::make()
                                                        ->title('Test webhook notification sent.')
                                                        ->success()
                                                        ->send();
                                                })
                                                ->hidden(fn (Forms\Get $get) => $get('webhook_enabled') !== true),
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
