<?php

namespace App\Filament\Pages\Settings;

use App\Forms\Components\TestDatabaseNotification;
use App\Forms\Components\TestMailNotification;
use App\Mail\Test;
use App\Settings\NotificationSettings;
use Closure;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\View;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Mail;

class NotificationPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Notifications';

    protected static ?string $navigationLabel = 'Notifications';

    protected static string $settings = NotificationSettings::class;

    protected function getFormSchema(): array
    {
        return [
            Grid::make([
                'default' => 1,
                'md' => 3,
            ])
                ->schema([
                    Grid::make([
                        'default' => 1,
                    ])
                    ->schema([
                        Section::make('Database')
                            ->description('Notifications sent to this channel will show up under the 🔔 icon in the header.')
                            ->schema([
                                Toggle::make('database_enabled')
                                    ->label('Enable database notifications')
                                    ->reactive()
                                    ->columnSpan(2),
                                Grid::make([
                                    'default' => 1,
                                ])
                                ->hidden(fn (Closure $get) => $get('database_enabled') !== true)
                                ->schema([
                                    Fieldset::make('Triggers')
                                        ->schema([
                                            Toggle::make('database_on_speedtest_run')
                                                ->label('Notify on every speetest run')
                                                ->columnSpan(2),
                                            Toggle::make('database_on_threshold_failure')
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

                        Section::make('Mail')
                            ->schema([
                                Toggle::make('mail_enabled')
                                    ->label('Enable mail notifications')
                                    ->reactive()
                                    ->columnSpan(2),
                                Grid::make([
                                    'default' => 1,
                                ])
                                ->hidden(fn (Closure $get) => $get('mail_enabled') !== true)
                                ->schema([
                                    Fieldset::make('Triggers')
                                        ->schema([
                                            Toggle::make('mail_on_speedtest_run')
                                                ->label('Notify on every speetest run')
                                                ->columnSpan(2),
                                            Toggle::make('mail_on_threshold_failure')
                                                ->label('Notify on threshold failures')
                                                ->columnSpan(2),
                                        ]),
                                ]),
                                Repeater::make('mail_recipients')
                                    ->label('Recipients')
                                    ->schema([
                                        TextInput::make('email_address')
                                            ->email()
                                            ->required(),
                                    ])
                                    ->hidden(fn (Closure $get) => $get('mail_enabled') !== true)
                                    ->columnSpan(['md' => 2]),
                                TestMailNotification::make('test channel')
                                    ->hidden(fn (Closure $get) => $get('mail_enabled') !== true),
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

                    Card::make()
                        ->schema([
                            View::make('filament.forms.notifications-helptext'),
                        ])
                        ->columnSpan([
                            'md' => 1,
                        ]),
                ]),
        ];
    }

    public function sendTestDatabaseNotification()
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

    public function sendTestMailNotification()
    {
        $notificationSettings = new (NotificationSettings::class);

        if (! empty($notificationSettings->mail_recipients)) {
            foreach ($notificationSettings->mail_recipients as $recipient) {
                Mail::to($recipient)
                    ->send(new Test());
            }

            Notification::make()
                ->title('Test mail notification sent.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('You need to add recipients to receive mail notifications.')
                ->warning()
                ->send();
        }
    }
}
