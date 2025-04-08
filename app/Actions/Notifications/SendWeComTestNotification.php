<?php

namespace App\Actions\Notifications;

use Filament\Notifications\Notification;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\WebhookServer\WebhookCall;

class SendWeComTestNotification
{
    use AsAction;

    public function handle(array $webhooks)
    {
        if (! count($webhooks)) {
            Notification::make()
                ->title('You need to add wecom urls!')
                ->warning()
                ->send();

            return;
        }

        $payload = [
            'msgtype' => 'text',
            'text' => [
                'content' => '👋 Testing the WeCom notification channel on Speedtest Tracker.',
            ],
        ];

        foreach ($webhooks as $webhook) {
            WebhookCall::create()
                ->url($webhook['url'])
                ->payload($payload)
                ->doNotSign()
                ->dispatch();
        }

        Notification::make()
            ->title('Test wecom notification sent.')
            ->success()
            ->send();
    }
}
