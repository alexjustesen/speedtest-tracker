<?php

namespace App\Jobs\Notifications\Apprise;

use App\Helpers\Number;
use App\Models\Result;
use App\Services\Notifications\AppriseService;
use App\Settings\NotificationSettings;
use App\Settings\ThresholdSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SendSpeedtestThresholdNotification implements ShouldQueue
{
    use Dispatchable, Queueable;

    public Result $result;

    /**
     * Create a new job instance.
     */
    public function __construct(Result $result)
    {
        $this->result = $result;
    }

    /**
     * Handle the event.
     */
    public function handle(): void
    {
        $settings = app(NotificationSettings::class);

        // If apprise_channel_urls is empty or not an array, skip
        if (empty($settings->apprise_channel_urls) || ! is_array($settings->apprise_channel_urls)) {
            Log::warning('Apprise service URLs not found; check Apprise notification settings.');

            return;
        }

        $thresholdSettings = app(ThresholdSettings::class);

        if (! $thresholdSettings->absolute_enabled) {

            return;
        }

        $failed = [];

        if ($thresholdSettings->absolute_download > 0) {
            array_push($failed, $this->absoluteDownloadThreshold($thresholdSettings));
        }

        if ($thresholdSettings->absolute_upload > 0) {
            array_push($failed, $this->absoluteUploadThreshold($thresholdSettings));
        }

        if ($thresholdSettings->absolute_ping > 0) {
            array_push($failed, $this->absolutePingThreshold($thresholdSettings));
        }

        $failed = array_filter($failed);

        if (! count($failed)) {
            Log::warning('Failed apprise thresholds not found, won\'t send notification.');

            return;
        }

        $payloadBody = view('apprise.speedtest-threshold', [
            'id' => $this->result->id,
            'service' => Str::title($this->result->service->getLabel()),
            'serverName' => $this->result->server_name,
            'serverId' => $this->result->server_id,
            'isp' => $this->result->isp,
            'metrics' => $failed,
            'speedtest_url' => $this->result->result_url,
            'url' => url('/admin/results'),
        ])->render();

        $payload = [
            'body' => $payloadBody,
            'title' => 'Speedtest Threshold Breach – #'.$this->result->id,
            'type' => 'info',
        ];

        // Send it!
        AppriseService::send($payload);
    }

    protected function absoluteDownloadThreshold(ThresholdSettings $thresholdSettings): bool|array
    {
        if (! absoluteDownloadThresholdFailed($thresholdSettings->absolute_download, $this->result->download)) {

            return false;
        }

        return [
            'name' => 'Download',
            'threshold' => $thresholdSettings->absolute_download.' Mbps',
            'value' => Number::toBitRate(bits: $this->result->download_bits, precision: 2),
        ];
    }

    protected function absoluteUploadThreshold(ThresholdSettings $thresholdSettings): bool|array
    {
        if (! absoluteUploadThresholdFailed($thresholdSettings->absolute_upload, $this->result->upload)) {

            return false;
        }

        return [
            'name' => 'Upload',
            'threshold' => $thresholdSettings->absolute_upload.' Mbps',
            'value' => Number::toBitRate(bits: $this->result->upload_bits, precision: 2),
        ];
    }

    protected function absolutePingThreshold(ThresholdSettings $thresholdSettings): bool|array
    {
        if (! absolutePingThresholdFailed($thresholdSettings->absolute_ping, $this->result->ping)) {

            return false;
        }

        return [
            'name' => 'Ping',
            'threshold' => $thresholdSettings->absolute_ping.' ms',
            'value' => round($this->result->ping, 2).' ms',
        ];
    }
}
