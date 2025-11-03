<?php

namespace App\Services\Notifications;

use App\Enums\UserRole;
use App\Models\User;
use App\Settings\NotificationSettings;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AppriseService
{
    /**
     * Send the given $payload (array) to every Apprise channel URL.
     * If a request fails, log it and notify admins.
     *
     * @param  array  $payload  Must include keys: 'body', 'title', 'type'.
     */
    public static function send(array $payload): void
    {
        $settings = app(NotificationSettings::class);

        if (
            empty($settings->apprise_channel_urls) ||
            ! is_array($settings->apprise_channel_urls)
        ) {
            Log::warning('Apprise service URLs not found; check Apprise settings.');

            return;
        }

        $instance = rtrim($settings->apprise_url, '/');

        foreach ($settings->apprise_channel_urls as $row) {
            $channelUrl = $row['channel_url'] ?? null;
            if (! $channelUrl) {
                Log::warning('Skipping entry with missing channel_url.');

                continue;
            }

            // Merge the channel into the payload
            $payload['urls'] = $channelUrl;

            try {
                $request = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ]);

                // If SSL verification is disabled in settings, skip it
                if (! $settings->apprise_verify_ssl) {
                    $request = $request->withoutVerifying();
                }

                $request->post($instance, $payload)->throw();

                Log::info("Apprise notification sent → instance: {$instance}   service: {$channelUrl}");
            } catch (\Throwable $e) {
                Log::error("Apprise notification failed for channel {$channelUrl} via {$instance}: ".$e->getMessage());

                $admins = User::where('role', UserRole::Admin)->get();
                Notification::make()
                    ->title('Apprise Notification Failure')
                    ->danger()
                    ->body("Failed to send notification to {$channelUrl}. Check logs for details.")
                    ->sendToDatabase($admins);
            }
        }
    }
}
