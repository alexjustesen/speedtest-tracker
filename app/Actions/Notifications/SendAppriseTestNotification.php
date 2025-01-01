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
            $payload = [
                'body' => '👋 Testing the Apprise notification channel.',
            ];

            if ($webhook['notification_type'] === 'tags' && ! empty($webhook['tags'])) {
                $tags = is_string($webhook['tags']) ? explode(',', $webhook['tags']) : $webhook['tags'];
                $payload['tags'] = implode(',', array_map('trim', $tags));
            } elseif (! empty($webhook['service_url'])) {
                $payload['urls'] = $webhook['service_url'];
            } else {
                Notification::make()->title('Webhook is missing either tags or service URL!')->warning()->send();

                continue;
            }

            try {
                $response = $client->post(rtrim($webhook['url'], '/'), [
                    'form_params' => $payload,
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
