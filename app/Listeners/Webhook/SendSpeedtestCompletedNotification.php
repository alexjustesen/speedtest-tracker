<?php

namespace App\Listeners\Webhook;

use App\Events\SpeedtestCompleted;
use App\Settings\NotificationSettings;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Spatie\WebhookServer\WebhookCall;

class SendSpeedtestCompletedNotification
{
    /**
     * Handle the event.
     */
    public function handle(SpeedtestCompleted $event): void
    {
        $notificationSettings = new NotificationSettings;

        if (! $notificationSettings->webhook_enabled) {
            return;
        }

        if (! $notificationSettings->webhook_on_speedtest_run) {
            return;
        }

        if (! count($notificationSettings->webhook_urls)) {
            Log::warning('Webhook urls not found, check webhook notification channel settings.');

            return;
        }

        foreach ($notificationSettings->webhook_urls as $url) {
            WebhookCall::create()
                ->url($url['url'])
                ->payload([
                    'result_id' => $event->result->id,
                    'site_name' => config('app.name'),
                    'server_name' => Arr::get($event->result->data, 'server.name'),
                    'server_id' => Arr::get($event->result->data, 'server.id'),
                    'isp' => Arr::get($event->result->data, 'isp'),
                    'ping' => $event->result->ping,
                    'download' => $event->result->downloadBits,
                    'upload' => $event->result->uploadBits,
                    'packet_loss' => Arr::get($event->result->data, 'packetLoss'),
                    'speedtest_url' => Arr::get($event->result->data, 'result.url'),
                    'url' => url('/admin/results'),
                ])
                ->doNotSign()
                ->dispatch();
        }
    }
}
