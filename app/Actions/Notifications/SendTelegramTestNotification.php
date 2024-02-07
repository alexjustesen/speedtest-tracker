<?php

namespace App\Actions\Notifications;

use App\Notifications\Telegram\TestNotification;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use Lorisleiva\Actions\Concerns\AsAction;

class SendTelegramTestNotification
{
    use AsAction;

    public function handle(array $recipients)
    {
        if (! count($recipients)) {
            Notification::make()
                ->title('You need to add Telegram recipients!')
                ->warning()
                ->send();

            return;
        }

        foreach ($recipients as $recipient) {
            FacadesNotification::route('telegram_chat_id', $recipient['telegram_chat_id'])
                ->notify(new TestNotification);
        }

        Notification::make()
            ->title('Test Telegram notification sent.')
            ->success()
            ->send();
    }
}
