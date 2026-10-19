<?php

namespace App\Notifications\Drivers;

use App\Mail\NotificationMail;
use App\Models\NotificationChannel;
use App\Notifications\NotificationMessage;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class MailDriver extends NotificationDriver
{
    public function configSchema(): array
    {
        return [
            Repeater::make('recipients')
                ->label(__('notification_channels.recipients'))
                ->simple(
                    TextInput::make('email')
                        ->placeholder('your@email.com')
                        ->email()
                        ->required(),
                )
                ->minItems(1)
                ->required(),
        ];
    }

    public function send(NotificationChannel $channel, NotificationMessage $message): void
    {
        $recipients = array_filter($channel->config('recipients', []));

        if (empty($recipients)) {
            throw new RuntimeException(__('notification_channels.errors.no_recipients'));
        }

        // Send one mail per recipient so recipients don't see each other.
        foreach ($recipients as $recipient) {
            Mail::to($recipient)->send(new NotificationMail($message));
        }
    }
}
