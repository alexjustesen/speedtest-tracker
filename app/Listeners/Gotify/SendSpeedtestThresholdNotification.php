<?php

namespace App\Listeners\Gotify;

use App\Events\SpeedtestCompleted;
use App\Helpers\Number;
use App\Services\SpeedtestThresholdNotificationPayload;
use App\Settings\NotificationSettings;
use App\Settings\ThresholdSettings;
use Gotify\Auth\Token;
use Gotify\Endpoint\Message;
use Gotify\Exception\EndpointException;
use Gotify\Exception\GotifyException;
use Gotify\Server;
use Illuminate\Support\Facades\Log;

class SendSpeedtestThresholdNotification
{
    protected $payloadService;

    public function __construct(SpeedtestThresholdNotificationPayload $payloadService)
    {
        $this->payloadService = $payloadService;
    }

    /**
     * Handle the event.
     */
    public function handle(SpeedtestCompleted $event): void
    {
        $notificationSettings = new NotificationSettings();

        if (! $notificationSettings->gotify_enabled) {
            return;
        }

        if (! $notificationSettings->gotify_on_threshold_failure) {
            return;
        }

        if (! count($notificationSettings->gotify_webhooks)) {
            Log::warning('Gotify URLs not found, check Gotify notification channel settings.');

            return;
        }

        $thresholdSettings = new ThresholdSettings();

        if (! $thresholdSettings->absolute_enabled) {
            return;
        }
        // Define the view name directly
        $viewName = 'notifications.speedtest-threshold';

        $payload = $this->payloadService->generateThresholdPayload($event, $thresholdSettings, $viewName);
        $extras = [
            'client::display' => [
                'contentType' => 'text/markdown',
            ],
        ];

        foreach ($notificationSettings->gotify_webhooks as $webhook) {
            try {
                $server = new Server($webhook['url']);
                $auth = new Token($webhook['token']);
                $message = new Message($server, $auth);

                $message->create(
                    title: 'Speedtest Threshold Alert',
                    message: $payload,
                    priority: Message::PRIORITY_HIGH,
                    extras: $extras,
                );

            } catch (EndpointException|GotifyException $err) {
                Log::error('Failed to send Gotify notification: '.$err->getMessage());
            }
        }
    }

    /**
     * Build Gotify notification if absolute download threshold is breached.
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
     * Build Gotify notification if absolute upload threshold is breached.
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
     * Build Gotify notification if absolute ping threshold is breached.
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
