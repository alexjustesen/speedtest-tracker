<?php

namespace App\Listeners\Webhook;

use App\Events\SpeedtestCompleted;
use App\Helpers\Number;
use App\Settings\NotificationSettings;
use App\Settings\ThresholdSettings;
use Illuminate\Support\Facades\Log;
use Spatie\WebhookServer\WebhookCall;

class SendSpeedtestThresholdNotification
{
    /**
     * Handle the event.
     */
    public function handle(SpeedtestCompleted $event): void
    {
        $notificationSettings = new NotificationSettings;

        if (! $notificationSettings->webhook_enabled) {
            return;
        }

        if (! $notificationSettings->webhook_on_threshold_failure) {
            return;
        }

        if (! count($notificationSettings->webhook_urls)) {
            Log::warning('Webhook urls not found, check webhook notification channel settings.');

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
            Log::warning('Failed webhook thresholds not found, won\'t send notification.');

            return;
        }

        foreach ($notificationSettings->webhook_urls as $url) {
            WebhookCall::create()
                ->url($url['url'])
                ->payload([
                    'result_id' => $event->result->id,
                    'site_name' => config('app.name'),
                    'isp' => $event->result->isp,
                    'metrics' => $failed,
                    'speedtest_url' => $event->result->result_url,
                    'url' => url('/admin/results'),
                ])
                ->doNotSign()
                ->dispatch();
        }
    }

    /**
     * Build webhook notification if absolute download threshold is breached.
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
     * Build webhook notification if absolute upload threshold is breached.
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
     * Build webhook notification if absolute ping threshold is breached.
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
