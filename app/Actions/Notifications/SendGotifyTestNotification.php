<?php

namespace App\Actions\Notifications;

use Filament\Notifications\Notification;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\WebhookServer\WebhookCall;

class SendGotifyTestNotification
{
    use AsAction;

    public function handle(array $webhooks)
    {
        if (! count($webhooks)) {
            Notification::make()
                ->title('You need to add Gotify urls!')
                ->warning()
                ->send();

            return;
        }

        foreach ($webhooks as $webhook) {
            WebhookCall::create()
                ->url($webhook['url'])
                ->payload(['message' => '👋 Testing the Gotify notification channel.'])
                ->doNotSign()
                ->dispatch();
        }

        Notification::make()
            ->title('Test Gotify notification sent.')
            ->success()
            ->send();
    }
}
