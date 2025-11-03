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
                ->title(__('notifications.pushover.add'))
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
                    'message' => __('notifications.pushover.payload'),
                ])
                ->doNotSign()
                ->dispatch();
        }

        Notification::make()
            ->title(__('notifications.pushover.sent'))
            ->success()
            ->send();
    }
}
