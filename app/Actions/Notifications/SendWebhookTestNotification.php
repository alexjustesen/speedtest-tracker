<?php

namespace App\Actions\Notifications;

use App\Models\Result;
use App\Services\SpeedtestFakeResultGenerator;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
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

        foreach ($webhooks as $webhook) {
            WebhookCall::create()
                ->url($webhook['url'])
                ->payload([
                    'result_id' => Str::uuid(),
                    'site_name' => __('settings/notifications.test_notifications.webhook.payload'),
                    'server_name' => $fakeResult->data['server']['name'],
                    'server_id' => $fakeResult->data['server']['id'],
                    'isp' => $fakeResult->data['isp'],
                    'ping' => $fakeResult->ping,
                    'download' => $fakeResult->download,
                    'upload' => $fakeResult->upload,
                    'packet_loss' => $fakeResult->data['packetLoss'],
                    'speedtest_url' => $fakeResult->data['result']['url'],
                    'url' => url('/admin/results'),
                ])
                ->doNotSign()
                ->dispatch();
        }

        Notification::make()
            ->title(__('settings/notifications.test_notifications.webhook.sent'))
            ->success()
            ->send();
    }
}
