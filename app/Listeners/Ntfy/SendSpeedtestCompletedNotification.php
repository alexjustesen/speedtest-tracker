<?php

namespace App\Listeners\Ntfy;

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

        if (! $notificationSettings->ntfy_enabled) {
            return;
        }

        if (! $notificationSettings->ntfy_on_speedtest_run) {
            return;
        }

        if (! count($notificationSettings->ntfy_webhooks)) {
            Log::warning('Ntfy urls not found, check Ntfy notification channel settings.');

            return;
        }

        $payload =
             view('ntfy.speedtest-completed', [
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
             ])->render();

        foreach ($notificationSettings->ntfy_webhooks as $url) {
            $webhookCall = WebhookCall::create()
                ->url($url['url'])
                ->payload([
                    'topic' => $url['topic'],
                    'message' => $payload,
                ])
                ->doNotSign();

            // Only add authentication if username and password are provided
            if (! empty($url['username']) && ! empty($url['password'])) {
                $authHeader = 'Basic '.base64_encode($url['username'].':'.$url['password']);
                $webhookCall->withHeaders([
                    'Authorization' => $authHeader,
                ]);
            }
            $webhookCall->dispatch();
        }
    }
}
