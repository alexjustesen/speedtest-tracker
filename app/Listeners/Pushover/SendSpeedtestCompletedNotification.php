<?php

namespace App\Listeners\Pushover;

use App\Events\SpeedtestCompleted;
use App\Services\SpeedtestCompletedNotificationPayload;
use App\Settings\NotificationSettings;
use Illuminate\Support\Facades\Log;
use Serhiy\Pushover\Api\Message\Message;
use Serhiy\Pushover\Api\Message\Notification as PushoverNotification;
use Serhiy\Pushover\Application;
use Serhiy\Pushover\Recipient;

class SendSpeedtestCompletedNotification
{
    protected $payloadService;

    public function __construct(SpeedtestCompletedNotificationPayload $payloadService)
    {
        $this->payloadService = $payloadService;
    }

    /**
     * Handle the event.
     */
    public function handle(SpeedtestCompleted $event): void
    {
        $notificationSettings = new NotificationSettings;

        // Check if Pushover notifications are enabled
        if (! $notificationSettings->pushover_enabled || ! $notificationSettings->pushover_on_speedtest_run) {
            return;
        }

        // Check if there are any Pushover webhooks
        if (! count($notificationSettings->pushover_webhooks)) {
            Log::warning('Pushover URLs not found, check Pushover notification channel settings.');

            return;
        }

        // Define the view name directly
        $viewName = 'pushover.speedtest-completed';

        // Generate the payload using the specified view
        $payload = $this->payloadService->generateSpeedtestPayload($event, $viewName);

        foreach ($notificationSettings->pushover_webhooks as $webhook) {
            try {
                // Create Application and Recipient objects
                $application = new Application($webhook['api_token']);
                $recipient = new Recipient($webhook['user_key']);

                // Compose the message with the payload as the body and a title
                $message = new Message($payload, 'Speedtest Completed Notification');
                $message->setIsHtml(true);

                // Create a notification with the application, recipient, and message
                $pushoverNotification = new PushoverNotification($application, $recipient, $message);

                // Push the notification
                /** @var \Serhiy\Pushover\Client\Response\MessageResponse $response */
                $response = $pushoverNotification->push();

                // Check response status
                if (! $response->isSuccessful()) {
                    Log::error('Failed to send Pushover notification: '.$response->getMessage());
                }
            } catch (\Exception $e) {
                Log::error('An error occurred while sending Pushover notification: '.$e->getMessage());
            }
        }
    }
}
