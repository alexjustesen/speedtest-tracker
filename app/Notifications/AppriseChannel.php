<?php

namespace App\Notifications;

use App\Settings\NotificationSettings;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        $appriseUrl = rtrim(config('services.apprise.url'), '/');
        $settings = app(NotificationSettings::class);

        try {
            $request = Http::timeout(5)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ]);

            // If SSL verification is disabled in settings, skip it
            if (! $settings->apprise_verify_ssl) {
                $request = $request->withoutVerifying();
            }

            $response = $request->post("{$appriseUrl}/notify", [
                'urls' => $message->urls,
                'title' => $message->title,
                'body' => $message->body,
                'type' => $message->type ?? 'info',
                'format' => $message->format ?? 'text',
                'tag' => $message->tag ?? null,
            ]);

            if ($response->failed()) {
                Log::error('Apprise notification failed', [
                    'channel' => $message->urls,
                    'instance' => $appriseUrl,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            } else {
                Log::info('Apprise notification sent', [
                    'channel' => $message->urls,
                    'instance' => $appriseUrl,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Apprise notification exception', [
                'channel' => $message->urls ?? 'unknown',
                'instance' => $appriseUrl,
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);
        }
    }
}
