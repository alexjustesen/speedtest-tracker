<?php

namespace App\Listeners\Apprise;

use App\Events\SpeedtestCompleted;
use App\Helpers\Number;
use App\Notifications\Apprise\SpeedtestNotification;
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

        if (! $notificationSettings->apprise_enabled) {
            return;
        }

        if (! $notificationSettings->apprise_on_threshold_failure) {
            return;
        }

        if (empty($notificationSettings->apprise_channel_urls) || ! is_array($notificationSettings->apprise_channel_urls)) {
            Log::warning('Apprise service URLs not found; check Apprise notification settings.');

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
            Log::warning('Failed Apprise thresholds not found, won\'t send notification.');

            return;
        }

        $body = view('apprise.speedtest-threshold', [
            'id' => $event->result->id,
            'service' => Str::title($event->result->service->getLabel()),
            'serverName' => $event->result->server_name,
            'serverId' => $event->result->server_id,
            'isp' => $event->result->isp,
            'metrics' => $failed,
            'speedtest_url' => $event->result->result_url,
            'url' => url('/admin/results'),
        ])->render();

        $title = 'Speedtest Threshold Breach – #'.$event->result->id;

        // Send notification to each configured channel URL
        foreach ($notificationSettings->apprise_channel_urls as $row) {
            $channelUrl = $row['channel_url'] ?? null;
            if (! $channelUrl) {
                Log::warning('Skipping entry with missing channel_url.');

                continue;
            }

            Notification::route('apprise_urls', $channelUrl)
                ->notify(new SpeedtestNotification($title, $body, 'warning'));
        }
    }

    /**
     * Build Apprise notification if absolute download threshold is breached.
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
     * Build Apprise notification if absolute upload threshold is breached.
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
     * Build Apprise notification if absolute ping threshold is breached.
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
