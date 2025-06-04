<?php

namespace App\Actions\Notifications;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class SendAppriseTestNotification
{
    use AsAction;

    public function handle(array $webhooks)
    {
        if (! count($webhooks)) {
            Notification::make()
                ->title('You need to add Apprise webhooks!')
                ->warning()
                ->send();

            return;
        }

        foreach ($webhooks as $webhook) {
            if (empty($webhook['url'])) {
                Notification::make()
                    ->title('Webhook is missing service URL!')
                    ->warning()
                    ->send();

                continue;
            }

            $payload = [
                'body' => '👋 Testing the Apprise notification channel.',
                'urls' => $webhook['service_url'],
            ];

            try {
                Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post(rtrim($webhook['url'], '/'), $payload)
                    ->throw();

                Notification::make()
                    ->title('Apprise notification sent successfully.')
                    ->success()
                    ->send();
            } catch (\Throwable $e) {
                Log::error('Apprise notification failed for URL '.$webhook['url'].': '.$e->getMessage());

                Notification::make()
                    ->title('Failed to send Apprise notification.')
                    ->warning()
                    ->body('An error occurred. Please check the logs for details.')
                    ->send();
            }
        }
    }
}
