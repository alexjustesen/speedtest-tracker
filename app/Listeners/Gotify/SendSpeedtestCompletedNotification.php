<?php

namespace App\Listeners\Gotify;

use App\Events\SpeedtestCompleted;
use App\Helpers\Number;
use App\Settings\NotificationSettings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\WebhookServer\WebhookCall;

class SendSpeedtestCompletedNotification
{
    /**
     * Handle the event.
     */
    public function handle(SpeedtestCompleted $event): void
    {
        $notificationSettings = new NotificationSettings;

        if (! $notificationSettings->gotify_enabled) {
            return;
        }

        if (! $notificationSettings->gotify_on_speedtest_run) {
            return;
        }

        if (! count($notificationSettings->gotify_webhooks)) {
            Log::warning('Gotify urls not found, check Gotify notification channel settings.');

            return;
        }

        $payload = [
            'message' => view('gotify.speedtest-completed', [
                'id' => $event->result->id,
                'service' => Str::title($event->result->service),
                'serverName' => $event->result->server_name,
                'serverId' => $event->result->server_id,
                'isp' => $event->result->isp,
                'ping' => round($event->result->ping).' ms',
                'download' => Number::toBitRate(bits: $event->result->download_bits, precision: 2),
                'upload' => Number::toBitRate(bits: $event->result->upload_bits, precision: 2),
                'packetLoss' => $event->result->packet_loss,
                'speedtest_url' => $event->result->result_url,
                'url' => url('/admin/results'),
            ])->render(),
        ];

        foreach ($notificationSettings->gotify_webhooks as $url) {
            WebhookCall::create()
                ->url($url['url'])
                ->payload($payload)
                ->doNotSign()
                ->dispatch();
        }
    }
}
