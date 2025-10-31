<?php

namespace App\Actions\Notifications;

use Filament\Notifications\Notification;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\WebhookServer\WebhookCall;

class SendDiscordTestNotification
{
    use AsAction;

    public function handle(array $webhooks)
    {
        if (! count($webhooks)) {
            Notification::make()
                ->title(__('translations.notifications.discord.add'))
                ->warning()
                ->send();

            return;
        }

        foreach ($webhooks as $webhook) {
            WebhookCall::create()
                ->url($webhook['url'])
                ->payload(['content' => __('translations.notifications.discord.payload')])
                ->doNotSign()
                ->dispatch();
        }

        Notification::make()
            ->title(__('translations.notifications.discord.sent'))
            ->success()
            ->send();
    }
}
