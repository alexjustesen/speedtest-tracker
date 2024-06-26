<?php

namespace App\Listeners\Teams;

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
        $notificationSettings = new NotificationSettings();

        if (! $notificationSettings->teams_enabled) {
            return;
        }

        if (! $notificationSettings->teams_on_speedtest_run) {
            return;
        }

        if (! count($notificationSettings->teams_webhooks)) {
            Log::warning('Teams urls not found, check Teams notification channel settings.');

            return;
        }

        $payload = [
            'text' => view('teams.speedtest-completed', [
                'id' => $event->result->id,
                'service' => Str::title($event->result->service),
                'serverName' => $event->result->server_name,
                'serverId' => $event->result->server_id,
                'isp' => $event->result->isp,
                'ping' => round($event->result->ping).' ms',
                'download' => Number::toBitRate(bits: $event->result->download_bits, precision: 2),
                'upload' => Number::toBitRate(bits: $event->result->upload_bits, precision: 2),
                'packetLoss' => $event->result->packet_loss,
                'url' => url('/admin/results'),
            ])->render(),
        ];

        foreach ($notificationSettings->teams_webhooks as $url) {
            WebhookCall::create()
                ->url($url['url'])
                ->payload($payload)
                ->doNotSign()
                ->dispatch();
        }
    }
}
