<?php

namespace App\Actions\Notifications;

use App\Models\Result;
use App\Services\SpeedtestFakeResultGenerator;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;
use Spatie\WebhookServer\WebhookCall;

class SendWebhookTestNotification
{
    use AsAction;

    public function handle(array $webhooks)
    {
        if (! count($webhooks)) {
            Notification::make()
                ->title(__('settings/notifications.test_notifications.webhook.add'))
                ->warning()
                ->send();

            return;
        }

        // Generate a fake Result (NOT saved to database)
        $fakeResult = SpeedtestFakeResultGenerator::completed();

        $hasFailure = false;

        foreach ($webhooks as $webhook) {
            $url = $webhook['url'];
            $succeeded = false;

            // Listen for success/failure events for this specific webhook
            Event::listen(WebhookCallSucceededEvent::class, function ($event) use ($url, &$succeeded) {
                if ($event->webhookUrl === $url) {
                    $succeeded = true;
                }
            });

            Event::listen(WebhookCallFailedEvent::class, function ($event) use ($url, &$succeeded) {
                if ($event->webhookUrl === $url) {
                    $succeeded = false;
                }
            });

            // Send webhook synchronously to get immediate result
            WebhookCall::create()
                ->url($url)
                ->payload([
                    'result_id' => Str::uuid(),
                    'site_name' => __('settings/notifications.test_notifications.webhook.payload'),
                    'isp' => $fakeResult->data['isp'],
                    'ping' => $fakeResult->ping,
                    'download' => $fakeResult->download,
                    'upload' => $fakeResult->upload,
                    'packetLoss' => $fakeResult->data['packetLoss'],
                    'speedtest_url' => $fakeResult->data['result']['url'],
                    'url' => url('/admin/results'),
                ])
                ->doNotSign()
                ->dispatchSync();

            if (! $succeeded) {
                $hasFailure = true;
            }
        }

        // Show appropriate notification based on results
        if (! $hasFailure) {
            Notification::make()
                ->title(__('settings/notifications.test_notifications.webhook.sent'))
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title(__('settings/notifications.test_notifications.webhook.failed'))
                ->body(__('settings/notifications.test_notifications.webhook.failed_body'))
                ->danger()
                ->send();
        }
    }
}
