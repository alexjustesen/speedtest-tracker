<?php

namespace App\Listeners\Telegram;

use App\Events\SpeedtestCompleted;
use App\Helpers\Number;
use App\Notifications\Telegram\SpeedtestNotification;
use App\Settings\NotificationSettings;
use App\Settings\ThresholdSettings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class SendSpeedtestThresholdNotification
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

        if (! $notificationSettings->telegram_on_threshold_failure) {
            return;
        }

        if (! count($notificationSettings->telegram_recipients) > 0) {
            Log::warning('Telegram recipients not found, check Telegram notification channel settings.');

            return;
        }

        $thresholdSettings = new ThresholdSettings;

        if (! $thresholdSettings->absolute_enabled) {
            return;
        }

        $failed = [];

        if ($thresholdSettings->absolute_download > 0) {
            array_push($failed, $this->absoluteDownloadThreshold(event: $event, thresholdSettings: $thresholdSettings));
        }

        if ($thresholdSettings->absolute_upload > 0) {
            array_push($failed, $this->absoluteUploadThreshold(event: $event, thresholdSettings: $thresholdSettings));
        }

        if ($thresholdSettings->absolute_ping > 0) {
            array_push($failed, $this->absolutePingThreshold(event: $event, thresholdSettings: $thresholdSettings));
        }

        $failed = array_filter($failed);

        if (! count($failed)) {
            Log::warning('Failed Telegram thresholds not found, won\'t send notification.');

            return;
        }

        $content = view('telegram.speedtest-threshold', [
            'id' => $event->result->id,
            'service' => Str::title($event->result->service),
            'serverName' => $event->result->server_name,
            'serverId' => $event->result->server_id,
            'isp' => $event->result->isp,
            'metrics' => $failed,
            'speedtest_url' => $event->result->result_url,
            'url' => url('/admin/results'),
        ])->render();

        foreach ($notificationSettings->telegram_recipients as $recipient) {
            Notification::route('telegram_chat_id', $recipient['telegram_chat_id'])
                ->notify(new SpeedtestNotification($content, $notificationSettings->telegram_disable_notification));
        }
    }

    /**
     * Build Telegram notification if absolute download threshold is breached.
     */
    protected function absoluteDownloadThreshold(SpeedtestCompleted $event, ThresholdSettings $thresholdSettings): bool|array
    {
        if (! absoluteDownloadThresholdFailed($thresholdSettings->absolute_download, $event->result->download)) {
            return false;
        }

        return [
            'name' => 'Download',
            'threshold' => $thresholdSettings->absolute_download.' Mbps',
            'value' => Number::toBitRate(bits: $event->result->download_bits, precision: 2),
        ];
    }

    /**
     * Build Telegram notification if absolute upload threshold is breached.
     */
    protected function absoluteUploadThreshold(SpeedtestCompleted $event, ThresholdSettings $thresholdSettings): bool|array
    {
        if (! absoluteUploadThresholdFailed($thresholdSettings->absolute_upload, $event->result->upload)) {
            return false;
        }

        return [
            'name' => 'Upload',
            'threshold' => $thresholdSettings->absolute_upload.' Mbps',
            'value' => Number::toBitRate(bits: $event->result->upload_bits, precision: 2),
        ];
    }

    /**
     * Build Telegram notification if absolute ping threshold is breached.
     */
    protected function absolutePingThreshold(SpeedtestCompleted $event, ThresholdSettings $thresholdSettings): bool|array
    {
        if (! absolutePingThresholdFailed($thresholdSettings->absolute_ping, $event->result->ping)) {
            return false;
        }

        return [
            'name' => 'Ping',
            'threshold' => $thresholdSettings->absolute_ping.' ms',
            'value' => round($event->result->ping, 2).' ms',
        ];
    }
}
