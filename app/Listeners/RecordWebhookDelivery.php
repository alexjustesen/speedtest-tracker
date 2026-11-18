<?php

namespace App\Listeners;

use App\Models\NotificationChannel;
use Spatie\WebhookServer\Events\FinalWebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;

class RecordWebhookDelivery
{
    /**
     * Record a successful webhook delivery on its channel.
     */
    public function handleSucceeded(WebhookCallSucceededEvent $event): void
    {
        $this->channel($event->meta)?->markAsSent();
    }

    /**
     * Record a webhook delivery that failed after every retry on its channel.
     */
    public function handleFailed(FinalWebhookCallFailedEvent $event): void
    {
        $this->channel($event->meta)?->markAsFailed(
            $event->errorMessage ?? __('notification_channels.errors.webhook_failed', ['status' => $event->response?->getStatusCode() ?? '-']),
        );
    }

    /**
     * Resolve the channel the webhook call was made for, if any.
     *
     * @param  array<string, mixed>  $meta
     */
    private function channel(array $meta): ?NotificationChannel
    {
        $id = $meta['notification_channel_id'] ?? null;

        return $id ? NotificationChannel::find($id) : null;
    }
}
