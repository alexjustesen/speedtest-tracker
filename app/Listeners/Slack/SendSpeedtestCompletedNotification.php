<?php

namespace App\Listeners\Slack;

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

        if (! $notificationSettings->slack_enabled) {
            return;
        }

        if (! $notificationSettings->slack_on_speedtest_run) {
            return;
        }

        if (! count($notificationSettings->slack_webhooks)) {
            Log::warning('Slack URLs not found, check Slack notification channel settings.');

            return;
        }

        $payload = [
            'text' => view('slack.speedtest-completed', [
                'id' => $event->result->id,
                'service' => Str::title($event->result->service->getLabel()),
                'serverName' => $event->result->server_name,
                'serverId' => $event->result->server_id,
                'isp' => $event->result->isp,
                'ping' => round($event->result->ping).' ms',
                'downloaded_bytes' => $event->result->downloaded_bytes,
                'downloaded_bytes' => Number::bytesToHuman(bytes: $event->result->downloaded_bytes, precision: 2),
                'uploaded_bytes' => Number::bytesToHuman(bytes: $event->result->uploaded_bytes, precision: 2),
                'uploaded_bytes' => $event->result->uploaded_bytes,
                'packetLoss' => $event->result->packet_loss,
                'speedtest_url' => $event->result->result_url,
                'url' => url('/admin/results'),
            ])->render(),
        ];

        foreach ($notificationSettings->slack_webhooks as $url) {
            WebhookCall::create()
                ->url($url['url'])
                ->payload($payload)
                ->doNotSign()
                ->dispatch();
        }
    }
}
