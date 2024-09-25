<?php

namespace App\Listeners\Pushover;

use App\Events\SpeedtestCompleted;
use App\Helpers\Number;
use App\Services\SpeedtestThresholdNotificationPayload;
use App\Settings\NotificationSettings;
use App\Settings\ThresholdSettings;
use Illuminate\Support\Facades\Log;
use Serhiy\Pushover\Api\Message\Message;
use Serhiy\Pushover\Api\Message\Notification as PushoverNotification;
use Serhiy\Pushover\Application;
use Serhiy\Pushover\Recipient;

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

        // Check if Pushover notifications are enabled and the threshold failure setting is enabled
        if (! $notificationSettings->pushover_enabled || ! $notificationSettings->pushover_on_threshold_failure) {
            return;
        }

        // Check if there are any Pushover webhooks
        if (! count($notificationSettings->pushover_webhooks)) {
            Log::warning('Pushover URLs not found, check Pushover notification channel settings.');

            return;
        }

        // Get the threshold settings
        $thresholdSettings = new ThresholdSettings();

        if (! $thresholdSettings->absolute_enabled) {
            return;
        }

        // Define the view name directly
        $viewName = 'pushover.speedtest-threshold';

        $payload = $this->payloadService->generateThresholdPayload($event, $thresholdSettings, $viewName);

        // Send the Pushover notification using the webhooks
        foreach ($notificationSettings->pushover_webhooks as $webhook) {
            try {
                // Create Application and Recipient objects
                $application = new Application($webhook['api_token']);
                $recipient = new Recipient($webhook['user_key']);

                // Create a message with the payload as the body and a title
                $message = new Message($payload, 'Speedtest Threshold Notification');
                $message->setIsHtml(true);

                // Create and send the Pushover notification
                $pushoverNotification = new PushoverNotification($application, $recipient, $message);

                /** @var \Serhiy\Pushover\Client\Response\MessageResponse $response */
                $response = $pushoverNotification->push();

                // Log if the notification failed
                if (! $response->isSuccessful()) {
                    Log::error('Failed to send Pushover notification: '.$response->getMessage());
                }
            } catch (\Exception $e) {
                Log::error('An error occurred while sending Pushover notification: '.$e->getMessage());
            }
        }
    }

    /**
     * Build Pushover notification if absolute download threshold is breached.
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
     * Build Pushover notification if absolute upload threshold is breached.
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
     * Build Pushover notification if absolute ping threshold is breached.
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
