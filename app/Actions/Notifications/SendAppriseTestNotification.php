<?php

namespace App\Actions\Notifications;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class SendAppriseTestNotification
{
    use AsAction;

    public function handle(string $apprise_url, bool $apprise_verify_ssl, array $channel_urls)
    {
        if (! $apprise_url) {
            Notification::make()
                ->title('You need to configure an Apprise URL!')
                ->warning()
                ->send();

            return;
        }

        foreach ($channel_urls as $row) {
            $serviceUrl = $row['channel_url'] ?? null;
            if (! $serviceUrl) {
                Notification::make()
                    ->title('Skipping missing Service URL!')
                    ->warning()
                    ->send();

                continue;
            }

            $payload = [
                'body' => '👋 Testing Apprise channel.',
                'urls' => $serviceUrl,
            ];

            try {
                $request = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ]);

                if (! $apprise_verify_ssl) {
                    $request = $request->withoutVerifying();
                }

                $request
                    ->post(rtrim($apprise_url, '/'), $payload)
                    ->throw();

                Notification::make()
                    ->title('Apprise notification sent successfully.')
                    ->success()
                    ->send();
            } catch (\Throwable $e) {
                Log::error('Apprise notification failed for service URL '.$serviceUrl.': '.$e->getMessage());

                Notification::make()
                    ->title('Failed to send Apprise notification.')
                    ->warning()
                    ->body('An error occurred. Please check the logs for details.')
                    ->send();
            }
        }
    }
}
