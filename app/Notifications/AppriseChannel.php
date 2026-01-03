<?php

namespace App\Notifications;

use App\Settings\NotificationSettings;
use Exception;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class AppriseChannel
{
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        // Get the Apprise message from the notification
        $message = $notification->toApprise($notifiable);

        if (! $message) {
            return;
        }

        $settings = app(NotificationSettings::class);
        $appriseUrl = $settings->apprise_server_url ?? '';

        if (empty($appriseUrl)) {
            Log::warning('Apprise notification skipped: No Server URL configured');

            return;
        }

        try {
            $request = Http::timeout(15)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ]);

            // If SSL verification is disabled in settings, skip it
            if (! $settings->apprise_verify_ssl) {
                $request = $request->withoutVerifying();
            }

            $response = $request->post($appriseUrl, [
                'urls' => $message->urls,
                'title' => $message->title,
                'body' => $message->body,
                'type' => $message->type ?? 'info',
                'format' => $message->format ?? 'text',
                'tag' => $message->tag ?? null,
            ]);

            // Only accept 200 OK responses as successful
            if ($response->status() !== 200) {
                throw new Exception('Apprise returned an error, please check Apprise logs for details');
            }

            Log::info('Apprise notification sent', [
                'channel' => $message->urls,
                'instance' => $appriseUrl,
            ]);
        } catch (Throwable $e) {
            Log::error('Apprise notification failed', [
                'channel' => $message->urls ?? 'unknown',
                'instance' => $appriseUrl,
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            // Re-throw the exception so it can be handled by the queue
            throw $e;
        }
    }
}
