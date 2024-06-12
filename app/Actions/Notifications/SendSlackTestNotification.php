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
                ->title('You need to add Slack URLs!')
                ->warning()
                ->send();

            return;
        }

        foreach ($webhooks as $webhook) {
            WebhookCall::create()
                ->url($webhook['url'])
                ->payload(['text' => '👋 Testing the Slack notification channel.'])
                ->doNotSign()
                ->dispatch();
        }

        Notification::make()
            ->title('Test Slack notification sent.')
            ->success()
            ->send();
    }
}
