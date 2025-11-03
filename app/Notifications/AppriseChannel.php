<?php

namespace App\Notifications;

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

        $appriseUrl = config('services.apprise.url');

        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                // ->when(true, function ($http) {
                //     $http->withoutVerifying();
                // })
                ->post("{$appriseUrl}/notify", [
                    'urls' => $message->urls,
                    'title' => $message->title,
                    'body' => $message->body,
                    'type' => $message->type ?? 'info',
                    'format' => $message->format ?? 'text',
                    'tag' => $message->tag ?? null,
                ]);

            if ($response->failed()) {
                Log::error('Apprise notification failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Apprise notification exception', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
