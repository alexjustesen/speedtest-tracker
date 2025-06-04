<?php

namespace App\Jobs\Notifications\Apprise;

use App\Enums\UserRole;
use App\Helpers\Number;
use App\Models\Result;
use App\Models\User;
use App\Settings\NotificationSettings;
use App\Settings\ThresholdSettings;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
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
        $notificationSettings = app(NotificationSettings::class);

        if (! count($notificationSettings->apprise_webhooks)) {
            Log::warning('Apprise URLs not found, check Apprise notification channel settings.');

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

        foreach ($notificationSettings->apprise_webhooks as $webhook) {
            if (empty($webhook['service_url']) || empty($webhook['url'])) {
                Log::warning('Webhook is missing service URL or URL, skipping.');

                continue;
            }

            $webhookPayload = [
                'body' => view('apprise.speedtest-threshold', [
                    'id' => $this->result->id,
                    'service' => Str::title($this->result->service->getLabel()),
                    'serverName' => $this->result->server_name,
                    'serverId' => $this->result->server_id,
                    'isp' => $this->result->isp,
                    'metrics' => $failed,
                    'speedtest_url' => $this->result->result_url,
                    'url' => url('/admin/results'),
                ])->render(),
                'title' => 'Speedtest Threshold Breach - #'.$this->result->id,
                'type' => 'info',
                'urls' => $webhook['service_url'],
            ];

            try {
                $request = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ]);
                if (empty($webhook['ssl_verify'])) {
                    $request = $request->withoutVerifying();
                }
                $request->post(rtrim($webhook['url'], '/'), $webhookPayload)
                    ->throw();

                Log::info('Apprise notification sent successfully to instance '.$webhook['url'].' and service url '.$webhook['service_url']);
            } catch (\Throwable $e) {
                Log::error('Apprise notification failed for instance '.$webhook['url'].' and service URL '.$webhook['service_url'].': '.$e->getMessage());

                // Notify admins if notifications fail.
                $admins = User::where('role', UserRole::Admin)->get();
                Notification::make()
                    ->title('Apprise Notification Failure')
                    ->danger()
                    ->body('Failed to send notification. Please check the logs.')
                    ->sendToDatabase($admins);
            }
        }
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
