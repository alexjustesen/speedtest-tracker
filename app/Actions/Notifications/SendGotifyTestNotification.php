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
                ->title(__('translations.notifications.gotfy.add'))
                ->warning()
                ->send();

            return;
        }

        foreach ($webhooks as $webhook) {
            WebhookCall::create()
                ->url($webhook['url'])
                ->payload(['message' => __('translations.notifications.gotfy.payload')])
                ->doNotSign()
                ->dispatch();
        }

        Notification::make()
            ->title(__('translations.notifications.gotfy.sent'))
            ->success()
            ->send();
    }
}
