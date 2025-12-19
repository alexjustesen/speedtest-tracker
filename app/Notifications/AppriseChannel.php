<?php

namespace App\Notifications;

use App\Settings\NotificationSettings;
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
        $appriseUrl = rtrim($settings->apprise_server_url ?? '', '/');

        if (empty($appriseUrl)) {
            Log::warning('Apprise notification skipped: No Server URL configured');

            return;
        }

        // Handle both cases: URL with or without /notify endpoint
        // If user already included /notify, don't append it again
        if (! str_ends_with($appriseUrl, '/notify')) {
            $appriseUrl .= '/notify';
        }

        try {
            $request = Http::timeout(5)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ]);

            // If SSL verification is disabled in settings, skip it
            if (! $settings->apprise_verify_ssl) {
                $request = $request->withoutVerifying();
            }

            $request->post($appriseUrl, [
                'urls' => $message->urls,
                'title' => $message->title,
                'body' => $message->body,
                'type' => $message->type ?? 'info',
                'format' => $message->format ?? 'text',
                'tag' => $message->tag ?? null,
            ])->throw();

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
