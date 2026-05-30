<?php

namespace App\Actions\Notifications;

use App\Actions\Webhooks\BuildWebhookPayload;
use App\Enums\WebhookEvent;
use App\Models\Webhook;
use App\Services\SpeedtestFakeResultGenerator;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\WebhookServer\WebhookCall;

class SendWebhookTestNotification
{
    use AsAction;

    public function handle(Webhook $webhook): void
    {
        // Generate a fake Result (NOT saved to the database).
        $fakeResult = SpeedtestFakeResultGenerator::completed();

        $payload = BuildWebhookPayload::run($fakeResult, WebhookEvent::Completed);

        $call = WebhookCall::create()
            ->url($webhook->url)
            ->payload($payload)
            ->meta([
                'webhook_test' => true,
                'user_id' => Auth::id(),
            ]);

        $webhook->secret
            ? $call->useSecret($webhook->secret)
            : $call->doNotSign();

        // Dispatch asynchronously; the delivery result is reported back to the
        // user as a database notification by NotifyWebhookTestResult.
        $call->dispatch();

        Notification::make()
            ->title(__('webhooks.test_queued'))
            ->body(__('webhooks.test_queued_body'))
            ->success()
            ->send();
    }
}
