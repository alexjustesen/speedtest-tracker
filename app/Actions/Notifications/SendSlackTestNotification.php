<?php

namespace App\Actions\Notifications;

use Filament\Notifications\Notification;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\WebhookServer\WebhookCall;

class SendSlackTestNotification
{
    use AsAction;

    public function handle(array $webhooks)
    {
        if (! count($webhooks)) {
            Notification::make()
                ->title(__('translations.notifications.slack.add'))
                ->warning()
                ->send();

            return;
        }

        foreach ($webhooks as $webhook) {
            WebhookCall::create()
                ->url($webhook['url'])
                ->payload(['text' => __('translations.notifications.slack.payload')])
                ->doNotSign()
                ->dispatch();
        }

        Notification::make()
            ->title(__('translations.notifications.slack.sent'))
            ->success()
            ->send();
    }
}
