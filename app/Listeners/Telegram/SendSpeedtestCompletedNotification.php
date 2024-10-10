<?php

namespace App\Listeners\Telegram;

use App\Events\SpeedtestCompleted;
use App\Helpers\Number;
use App\Notifications\Telegram\SpeedtestNotification;
use App\Settings\NotificationSettings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class SendSpeedtestCompletedNotification
{
    /**
     * Handle the event.
     */
    public function handle(SpeedtestCompleted $event): void
    {
        $notificationSettings = new NotificationSettings;

        if (! $notificationSettings->telegram_enabled) {
            return;
        }

        if (! $notificationSettings->telegram_on_speedtest_run) {
            return;
        }

        if (! count($notificationSettings->telegram_recipients)) {
            Log::warning('Telegram recipients not found, check Telegram notification channel settings.');

            return;
        }

        $content = view('telegram.speedtest-completed', [
            'id' => $event->result->id,
            'service' => Str::title($event->result->service),
            'serverName' => $event->result->server_name,
            'serverId' => $event->result->server_id,
            'isp' => $event->result->isp,
            'ping' => round($event->result->ping).' ms',
            'download' => Number::toBitRate(bits: $event->result->download_bits, precision: 2),
            'upload' => Number::toBitRate(bits: $event->result->upload_bits, precision: 2),
            'packetLoss' => is_numeric($event->result->packet_loss) ? round($event->result->packet_loss, 2) : 'n/a',
            'speedtest_url' => $event->result->result_url,
            'url' => url('/admin/results'),
        ])->render();

        foreach ($notificationSettings->telegram_recipients as $recipient) {
            Notification::route('telegram_chat_id', $recipient['telegram_chat_id'])
                ->notify(new SpeedtestNotification($content, $notificationSettings->telegram_disable_notification));
        }
    }
}
