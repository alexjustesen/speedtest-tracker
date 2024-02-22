<?php

namespace App\Actions\Notifications;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class SendDiscordTestNotification
{
    use AsAction;

    public function handle(array $webhooks)
    {
        if (! count($webhooks)) {
            Notification::make()
                ->title('You need to add webhook urls!')
                ->warning()
                ->send();

            return;
        }

        foreach ($webhooks as $webhook) {
            $payload = [
                'content' => '👋 Testing the Discord notification channel.',
            ];

            // Send the request using Laravel's HTTP client
            $response = Http::post($webhook['discord_webhook_url'], $payload);
        }

        Notification::make()
            ->title('Test Discord notification sent.')
            ->success()
            ->send();
    }
}
