<?php

namespace App\Actions\Notifications;

use Filament\Notifications\Notification as FilamentNotification;
use Lorisleiva\Actions\Concerns\AsAction;
use Serhiy\Pushover\Api\Message\Message;
use Serhiy\Pushover\Api\Message\Notification as PushoverNotification;
use Serhiy\Pushover\Application;
use Serhiy\Pushover\Recipient;

class SendPushoverTestNotification
{
    use AsAction;

    public function handle(array $webhooks)
    {
        if (! count($webhooks)) {
            FilamentNotification::make()
                ->title('You need to add Pushover credentials!')
                ->warning()
                ->send();

            return;
        }

        foreach ($webhooks as $webhook) {
            try {
                // Create Application and Recipient objects
                $application = new Application($webhook['api_token']);
                $recipient = new Recipient($webhook['user_key']);

                // Compose the message with title and body
                $message = new Message('👋 Testing the Pushover notification channel.', 'Speedtest Tracker Test Notification');

                // Create a notification with the application, recipient, and message
                $pushoverNotification = new PushoverNotification($application, $recipient, $message);

                // Push the notification
                /** @var \Serhiy\Pushover\Client\Response\MessageResponse $response */
                $response = $pushoverNotification->push();

                // Check response status
                if ($response->isSuccessful()) {
                    FilamentNotification::make()
                        ->title('Test Pushover notification sent.')
                        ->success()
                        ->send();
                } else {
                    FilamentNotification::make()
                        ->title('Failed to send Pushover notification: '.$response->getMessage())
                        ->danger()
                        ->send();
                }
            } catch (\Exception $e) {
                FilamentNotification::make()
                    ->title('An error occurred: '.$e->getMessage())
                    ->danger()
                    ->send();
            }
        }
    }
}
