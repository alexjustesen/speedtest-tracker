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
            $webhookCall = WebhookCall::create()
                ->url($webhook['url'])
                ->payload([
                    'topic' => $webhook['topic'],
                    'message' => '👋 Testing the ntfy notification channel.',
                ])
                ->doNotSign();

            // Only add authentication if username and password are provided
            if (! empty($webhook['username']) && ! empty($webhook['password'])) {
                $authHeader = 'Basic '.base64_encode($webhook['username'].':'.$webhook['password']);
                $webhookCall->withHeaders([
                    'Authorization' => $authHeader,
                ]);
            }

            $webhookCall->dispatch();
        }

        Notification::make()
            ->title('Test ntfy notification sent.')
            ->success()
            ->send();
    }
}
