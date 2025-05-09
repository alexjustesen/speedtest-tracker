<?php

namespace App\Actions\Notifications;

use Filament\Notifications\Notification;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Lorisleiva\Actions\Concerns\AsAction;

class SendAppriseTestNotification
{
    use AsAction;

    public function handle(array $webhooks)
    {
        if (! count($webhooks)) {
            Notification::make()->title('You need to add Apprise webhooks!')->warning()->send();

            return;
        }

        $client = new Client;

        foreach ($webhooks as $webhook) {
            if (empty($webhook['service_url'])) {
                Notification::make()->title('Webhook is missing service URL!')->warning()->send();

                continue;
            }

            $payload = [
                'body' => '👋 Testing the Apprise notification channel.',
                'urls' => $webhook['service_url'],
            ];

            try {
                $response = $client->post(rtrim($webhook['url'], '/'), [
                    'json' => $payload,
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]);

                if ($response->getStatusCode() === 200) {
                    Notification::make()->title('Apprise notification sent successfully.')->success()->send();
                } else {
                    Notification::make()
                        ->title('Failed to send Apprise notification.')
                        ->warning()
                        ->body('HTTP Status: '.$response->getStatusCode())
                        ->send();
                }
            } catch (RequestException $e) {
                Notification::make()
                    ->title('Failed to send Apprise notification.')
                    ->warning()
                    ->body($e->getMessage())
                    ->send();
            }
        }
    }
}
