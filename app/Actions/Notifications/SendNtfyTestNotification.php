<?php

namespace App\Actions\Notifications;

use Filament\Notifications\Notification;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\WebhookServer\WebhookCall;

class SendNtfyTestNotification
{
    use AsAction;

    public function handle(array $webhooks)
    {
        if (! count($webhooks)) {
            Notification::make()
                ->title('You need to add ntfy urls!')
                ->warning()
                ->send();

            return;
        }

        foreach ($webhooks as $webhook) {
            WebhookCall::create()
                ->url($webhook['url'])
                ->payload([
                    'topic' => $webhook['topic'],
                    'message' => '👋 Testing the Pushover notification channel.',
                ])
                ->doNotSign()
                ->dispatch();
        }

        Notification::make()
            ->title('Test ntfy notification sent.')
            ->success()
            ->send();
    }
}
