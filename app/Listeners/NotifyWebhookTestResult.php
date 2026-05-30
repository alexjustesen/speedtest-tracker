<?php

namespace App\Listeners;

use App\Models\User;
use Filament\Notifications\Notification;
use Spatie\WebhookServer\Events\FinalWebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallEvent;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;

class NotifyWebhookTestResult
{
    /**
     * Report a successful test webhook delivery.
     */
    public function handleSucceeded(WebhookCallSucceededEvent $event): void
    {
        $this->notify($event, success: true);
    }

    /**
     * Report a failed test webhook delivery.
     */
    public function handleFailed(FinalWebhookCallFailedEvent $event): void
    {
        $this->notify($event, success: false);
    }

    /**
     * Send a database notification to the user who triggered the test.
     */
    private function notify(WebhookCallEvent $event, bool $success): void
    {
        if (! ($event->meta['webhook_test'] ?? false)) {
            return;
        }

        $user = User::find($event->meta['user_id'] ?? null);

        if ($user === null) {
            return;
        }

        $notification = Notification::make()
            ->title($success ? __('webhooks.test_sent') : __('webhooks.test_failed'))
            ->body($success ? $event->webhookUrl : ($event->errorMessage ?? $event->webhookUrl));

        $success ? $notification->success() : $notification->danger();

        $notification->sendToDatabase($user);
    }
}
