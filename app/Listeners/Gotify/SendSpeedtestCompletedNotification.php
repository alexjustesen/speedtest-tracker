<?php

namespace App\Listeners\Gotify;

use App\Events\SpeedtestCompleted;
use App\Services\SpeedtestCompletedNotificationPayload;
use App\Settings\NotificationSettings;
use Gotify\Auth\Token;
use Gotify\Endpoint\Message;
use Gotify\Exception\EndpointException;
use Gotify\Exception\GotifyException;
use Gotify\Server;
use Illuminate\Support\Facades\Log;

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
        $notificationSettings = new NotificationSettings();

        if (! $notificationSettings->gotify_enabled) {
            return;
        }

        if (! $notificationSettings->gotify_on_speedtest_run) {
            return;
        }

        if (! count($notificationSettings->gotify_webhooks)) {
            Log::warning('Gotify URLs not found, check Gotify notification channel settings.');

            return;
        }
        // Define the view name directly
        $viewName = 'notifications.speedtest-completed';

        // Generate the payload using the specified view
        $payload = $this->payloadService->generateSpeedtestPayload($event, $viewName);

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
                    title: 'Speedtest Completed',
                    message: $payload,
                    extras: $extras,
                );

            } catch (EndpointException|GotifyException $err) {
                Log::error('Failed to send Gotify notification: '.$err->getMessage());
            }
        }
    }
}
