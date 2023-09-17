<?php

namespace App\Filament\Pages\Settings;

use App\Forms\Components\TestDatabaseNotification;
use App\Forms\Components\TestMailNotification;
use App\Forms\Components\TestTelegramNotification;
use App\Mail\Test;
use App\Notifications\Telegram\TestNotification as TelegramTestNotification;
use App\Settings\NotificationSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification as FacadesNotification;

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
        abort_unless(auth()->user()->is_admin, 403);

        $this->fillForm();
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
                                            ->columnSpan(2),
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                        ])
                                            ->hidden(fn (Forms\Get $get) => $get('database_enabled') !== true)
                                            ->schema([
                                                Forms\Components\Fieldset::make('Triggers')
                                                    ->schema([
                                                        Forms\Components\Toggle::make('database_on_speedtest_run')
                                                            ->label('Notify on every speedtest run')
                                                            ->columnSpan(2),
                                                        Forms\Components\Toggle::make('database_on_threshold_failure')
                                                            ->label('Notify on threshold failures')
                                                            ->columnSpan(2),
                                                    ]),
                                                TestDatabaseNotification::make('test channel'),
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
                                            ->columnSpan(2),
                                        Forms\Components\Grid::make([
                                            'default' => 1,
                                        ])
                                            ->schema([
                                                Forms\Components\Fieldset::make('Triggers')
                                                    ->schema([
                                                        Forms\Components\Toggle::make('mail_on_speedtest_run')
                                                            ->label('Notify on every speedtest run')
                                                            ->columnSpan(2),
                                                        Forms\Components\Toggle::make('mail_on_threshold_failure')
                                                            ->label('Notify on threshold failures')
                                                            ->columnSpan(2),
                                                    ]),
                                            ])
                                            ->hidden(fn (Forms\Get $get) => $get('mail_enabled') !== true),

                                        Forms\Components\Repeater::make('mail_recipients')
                                            ->label('Recipients')
                                            ->schema([
                                                Forms\Components\TextInput::make('email_address')
                                                    ->email()
                                                    ->required(),
                                            ])
                                            ->hidden(fn (Forms\Get $get) => $get('mail_enabled') !== true)
                                            ->columnSpan(['md' => 2]),
                                        TestMailNotification::make('test channel')
                                            ->hidden(fn (Forms\Get $get) => $get('mail_enabled') !== true),
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
                                            ->columnSpan(2),
                                        Forms\Components\Grid::make([
                                            'default' => 1, ])
                                            ->hidden(fn (Forms\Get $get) => $get('telegram_enabled') !== true)
                                            ->schema([
                                                Forms\Components\Fieldset::make('Triggers')
                                                    ->schema([
                                                        Forms\Components\Toggle::make('telegram_on_speedtest_run')
                                                            ->label('Notify on every speedtest run')
                                                            ->columnSpan(2),
                                                        Forms\Components\Toggle::make('telegram_on_threshold_failure')
                                                            ->label('Notify on threshold failures')
                                                            ->columnSpan(2),
                                                    ]),
                                            ]),
                                        Forms\Components\Repeater::make('telegram_recipients')
                                            ->label('Recipients')
                                            ->schema([
                                                Forms\Components\TextInput::make('telegram_chat_id')
                                                    ->maxLength(50)
                                                    ->required()
                                                    ->columnSpan(['md' => 2]),
                                            ])
                                            ->hidden(fn (Forms\Get $get) => $get('telegram_enabled') !== true)
                                            ->columnSpan(['md' => 2]),
                                        TestTelegramNotification::make('test channel')
                                            ->hidden(fn (Forms\Get $get) => $get('telegram_enabled') !== true),
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

    public function sendTestDatabaseNotification(): void
    {
        $recipient = auth()->user();

        $recipient->notify(
            Notification::make()
                ->title('Test database notification received!')
                ->body('You say pong')
                ->success()
                ->toDatabase(),
        );

        Notification::make()
            ->title('Test database notification sent.')
            ->body('I say ping')
            ->success()
            ->send();
    }

    public function sendTestMailNotification(): void
    {
        $notificationSettings = new (NotificationSettings::class);

        if (blank($notificationSettings->mail_recipients)) {
            Notification::make()
                ->title('You need to add mail recipients.')
                ->body('Make sure to click "Save changes" before testing mail notifications.')
                ->warning()
                ->send();

            return;
        }

        foreach ($notificationSettings->mail_recipients as $recipient) {
            Mail::to($recipient)
                ->send(new Test());
        }

        Notification::make()
            ->title('Test mail notification sent.')
            ->success()
            ->send();
    }

    public function sendTestTelegramNotification(): void
    {
        $notificationSettings = new (NotificationSettings::class);

        $bot = config('telegram.bot');

        if (blank($bot)) {
            Notification::make()
                ->title('No Telegram bot provided.')
                ->body('You need to add "TELEGRAM_BOT_TOKEN" in your .env file or add it as environment variable')
                ->danger()
                ->send();

            return;
        }

        if (blank($notificationSettings->telegram_recipients)) {
            Notification::make()
                ->title('You need to add Telegram recipients.')
                ->body('Make sure to click "Save changes" before testing Telegram notifications.')
                ->warning()
                ->send();

            return;
        }

        foreach ($notificationSettings->telegram_recipients as $recipient) {
            FacadesNotification::route('telegram_chat_id', $recipient['telegram_chat_id'])
                ->notify(new TelegramTestNotification);
        }

        Notification::make()
            ->title('Test Telegram notification sent.')
            ->success()
            ->send();
    }
}
