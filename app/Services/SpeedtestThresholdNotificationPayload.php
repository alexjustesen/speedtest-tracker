<?php

namespace App\Services;

use App\Events\SpeedtestCompleted;
use App\Helpers\Number;
use App\Settings\ThresholdSettings;
use Illuminate\Support\Str;

class SpeedtestThresholdNotificationPayload
{
    /**
     * Generate the payload for the speedtest threshold notification.
     */
    public function generateThresholdPayload(SpeedtestCompleted $event, ThresholdSettings $thresholdSettings): string
    {
        $failed = [];

        if ($thresholdSettings->absolute_download > 0) {
            $failed[] = $this->absoluteDownloadThreshold($event, $thresholdSettings);
        }

        if ($thresholdSettings->absolute_upload > 0) {
            $failed[] = $this->absoluteUploadThreshold($event, $thresholdSettings);
        }

        if ($thresholdSettings->absolute_ping > 0) {
            $failed[] = $this->absolutePingThreshold($event, $thresholdSettings);
        }

        $failed = array_filter($failed);

        return view('notifications.speedtest-threshold', [
            'id' => $event->result->id,
            'service' => Str::title($event->result->service),
            'serverName' => $event->result->server_name,
            'serverId' => $event->result->server_id,
            'isp' => $event->result->isp,
            'metrics' => $failed,
            'speedtest_url' => $event->result->result_url,
            'url' => url('/admin/results'),
        ])->render();
    }

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
