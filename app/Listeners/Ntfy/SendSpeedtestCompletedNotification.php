<?php

namespace App\Listeners\Ntfy;

use App\Events\SpeedtestCompleted;
use App\Services\SpeedtestCompletedNotificationPayload;
use App\Settings\NotificationSettings;
use Illuminate\Support\Facades\Log;
use Ntfy\Auth\Token;
use Ntfy\Auth\User;
use Ntfy\Client;
use Ntfy\Exception\EndpointException;
use Ntfy\Exception\NtfyException;
use Ntfy\Message;
use Ntfy\Server;

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

        if (! $notificationSettings->ntfy_enabled) {
            return;
        }

        if (! $notificationSettings->ntfy_on_speedtest_run) {
            return;
        }

        if (! count($notificationSettings->ntfy_webhooks)) {
            Log::warning('Ntfy URLs not found, check Ntfy notification channel settings.');

            return;
        }

        $payload = $this->payloadService->generateSpeedtestPayload($event);

        foreach ($notificationSettings->ntfy_webhooks as $url) {
            try {
                // Set server
                $server = new Server($url['url']);

                // Create a new message
                $message = new Message();
                $message->topic($url['topic']);
                $message->title('Speedtest Completed');
                $message->markdownBody($payload);

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
