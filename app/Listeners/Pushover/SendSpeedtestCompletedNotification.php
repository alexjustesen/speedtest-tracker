<?php

namespace App\Listeners\Pushover;

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

        if (! $notificationSettings->pushover_enabled) {
            return;
        }

        if (! $notificationSettings->pushover_on_speedtest_run) {
            return;
        }

        if (! count($notificationSettings->pushover_webhooks)) {
            Log::warning('Pushover urls not found, check Pushover notification channel settings.');

            return;
        }

        $payload = [
            view('pushover.speedtest-completed', [
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

        foreach ($notificationSettings->pushover_webhooks as $url) {
            WebhookCall::create()
                ->url($url['url'])
                ->payload([
                    'token' => $url['api_token'],
                    'user' => $url['user_key'],
                    'message' => $payload,
                ])
                ->doNotSign()
                ->dispatch();
        }
    }
}
