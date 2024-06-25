<?php

namespace App\Actions\Notifications;

use Filament\Notifications\Notification;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\WebhookServer\WebhookCall;

class SendPushoverTestNotification
{
    use AsAction;

    public function handle(array $webhooks)
    {
        if (! count($webhooks)) {
            Notification::make()
                ->title('You need to add Pushover URLs!')
                ->warning()
                ->send();

            return;
        }

        foreach ($webhooks as $webhook) {
            WebhookCall::create()
                ->url($webhook['url'])
                ->payload([
                    'token' => $webhook['api_token'],
                    'user' => $webhook['user_key'],
                    'message' => '👋 Testing the Pushover notification channel.',
                ])
                ->doNotSign()
                ->dispatch();
        }

        Notification::make()
            ->title('Test Pushover notification sent.')
            ->success()
            ->send();
    }
}
