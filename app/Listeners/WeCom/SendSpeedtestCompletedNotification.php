<?php

namespace App\Listeners\WeCom;

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

        if (! $notificationSettings->wecom_enabled) {
            return;
        }

        if (! $notificationSettings->wecom_on_speedtest_run) {
            return;
        }

        if (! count($notificationSettings->wecom_webhooks)) {
            Log::warning('WeCom urls not found, check wecom notification channel settings.');

            return;
        }

        $payload = [
            'msgtype' => 'markdown',
            'markdown' => [
                'content' => view('wecom.speedtest-completed', [
                    'id' => $event->result->id,
                    'service' => Str::title($event->result->service->getLabel()),
                    'serverName' => $event->result->server_name,
                    'serverId' => $event->result->server_id,
                    'isp' => $event->result->isp,
                    'ping' => round($event->result->ping).' ms',
                    'download' => Number::toBitRate(bits: $event->result->download_bits, precision: 2),
                    'upload' => Number::toBitRate(bits: $event->result->upload_bits, precision: 2),
                    'packetLoss' => is_numeric($event->result->packet_loss) ? round($event->result->packet_loss, 2) : 'n/a',
                    'speedtest_url' => $event->result->result_url,
                    'url' => url('/admin/results'),
                ])->render(),
            ],
        ];

        foreach ($notificationSettings->wecom_webhooks as $webhook) {
            WebhookCall::create()
                ->url($webhook['url'])
                ->payload($payload)
                ->doNotSign()
                ->dispatch();
        }
    }
}
