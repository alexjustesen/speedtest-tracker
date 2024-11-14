<?php

namespace App\Listeners\Ntfy;

use App\Events\SpeedtestCompleted;
use App\Services\SpeedtestThresholdNotificationPayload;
use App\Settings\NotificationSettings;
use App\Settings\ThresholdSettings;
use Illuminate\Support\Facades\Log;
use Ntfy\Auth\Token;
use Ntfy\Auth\User;
use Ntfy\Client;
use Ntfy\Exception\EndpointException;
use Ntfy\Exception\NtfyException;
use Ntfy\Message;
use Ntfy\Server;

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
        $notificationSettings = new NotificationSettings;

        if (! $notificationSettings->ntfy_enabled) {
            return;
        }

        if (! $notificationSettings->ntfy_on_threshold_failure) {
            return;
        }

        if (! count($notificationSettings->ntfy_webhooks)) {
            Log::warning('Ntfy URLs not found, check Ntfy notification channel settings.');

            return;
        }

        $thresholdSettings = new ThresholdSettings;

        if (! $thresholdSettings->absolute_enabled) {
            return;
        }

        // Define the view name directly
        $viewName = 'notifications.speedtest-threshold';

        $payload = $this->payloadService->generateThresholdPayload($event, $thresholdSettings, $viewName);

        if (empty($payload)) {
            Log::warning('Failed ntfy thresholds not found, won\'t send notification.');

            return;
        }

        foreach ($notificationSettings->ntfy_webhooks as $url) {
            try {
                // Set server
                $server = new Server($url['url']);

                // Create a new message
                $message = new Message;
                $message->topic($url['topic']);
                $message->title('Speedtest Threshold Alert');
                $message->markdownBody($payload);
                $message->priority(Message::PRIORITY_HIGH);

                // Set authentication if username/password or token is provided
                $auth = null;
                if (! empty($url['token'])) {
                    $auth = new Token($url['token']);
                } elseif (! empty($url['username']) && ! empty($url['password'])) {
                    $auth = new User($url['username'], $url['password']);
                }

                // Create a client with optional authentication
                $client = new Client($server, $auth);
                $client->send($message);

            } catch (EndpointException|NtfyException $err) {
                Log::error('Failed to send notification: '.$err->getMessage());
            }
        }
    }
}
