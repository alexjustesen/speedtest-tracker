<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;

class LogWebhookFailure
{
    /**
     * Handle the event.
     */
    public function handle(WebhookCallFailedEvent $event): void
    {
        Log::error('Webhook notification failed', [
            'url' => $event->webhookUrl,
            'error_type' => $event->errorType,
            'error_message' => $event->errorMessage,
        ]);
    }
}
