<?php

namespace App\Listeners\HealthCheck;

use App\Events\SpeedtestCompleted;
use App\Settings\NotificationSettings;
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

        if (! $notificationSettings->healthcheck_enabled) {
            return;
        }

        if (! $notificationSettings->healthcheck_on_speedtest_run) {
            return;
        }

        if (! count($notificationSettings->healthcheck_webhooks)) {
            Log::warning('healthcheck urls not found, check healthcheck notification channel settings.');

            return;
        }

        foreach ($notificationSettings->healthcheck_webhooks as $url) {
            WebhookCall::create()
                ->url($url['url'])
                ->payload([
                    'result_id' => $event->result->id,
                    'site_name' => config('app.name'),
                    'isp' => $event->result->isp,
                    'ping' => $event->result->ping,
                    'download' => $event->result->downloadBits,
                    'upload' => $event->result->uploadBits,
                    'packetLoss' => $event->result->packet_loss,
                    'speedtest_url' => $event->result->result_url,
                    'url' => url('/admin/results'),
                ])
                ->doNotSign()
                ->dispatch();
        }
    }
}
