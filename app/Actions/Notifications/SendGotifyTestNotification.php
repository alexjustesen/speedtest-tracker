<?php

namespace App\Actions\Notifications;

use Filament\Notifications\Notification;
use Gotify\Auth\Token;
use Gotify\Endpoint\Message;
use Gotify\Exception\EndpointException;
use Gotify\Exception\GotifyException;
use Gotify\Server;
use Lorisleiva\Actions\Concerns\AsAction;

class SendGotifyTestNotification
{
    use AsAction;

    public function handle(array $webhooks, string $token)
    {
        if (empty($webhooks)) {
            Notification::make()
                ->title('You need to add Gotify URLs!')
                ->warning()
                ->send();

            return;
        }

        foreach ($webhooks as $url) {
            try {
                // Set up the server and token
                $server = new Server($url);
                $auth = new Token($token);
                $message = new Message($server, $auth);

                // Send message
                $details = $message->create(
                    title: 'Test Notification',
                    message: '👋 Testing the Gotify notification channel',
                );

                // Optionally, you can log the sent message details
                // echo 'Id: ' . $details->id . PHP_EOL;

            } catch (EndpointException|GotifyException $err) {
                Notification::make()
                    ->title('Error sending Gotify notification: '.$err->getMessage())
                    ->danger()
                    ->send();
            }
        }

        Notification::make()
            ->title('Test Gotify notifications sent.')
            ->success()
            ->send();
    }
}
