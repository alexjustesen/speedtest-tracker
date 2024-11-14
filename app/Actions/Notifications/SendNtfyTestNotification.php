<?php

namespace App\Actions\Notifications;

use Filament\Notifications\Notification;
use Lorisleiva\Actions\Concerns\AsAction;
use Ntfy\Auth\Token;
use Ntfy\Auth\User;
use Ntfy\Client;
use Ntfy\Exception\EndpointException;
use Ntfy\Exception\NtfyException;
use Ntfy\Message;
use Ntfy\Server;

class SendNtfyTestNotification
{
    use AsAction;

    public function handle(array $webhooks)
    {
        if (! count($webhooks)) {
            Notification::make()
                ->title('You need to add ntfy URLs!')
                ->warning()
                ->send();

            return;
        }

        foreach ($webhooks as $webhook) {
            try {
                // Set server
                $server = new Server($webhook['url']);

                // Create a new message
                $message = new Message;
                $message->topic($webhook['topic']);
                $message->title('Test Notification');
                $message->body('👋 Testing the ntfy notification channel.');

                $auth = null;
                if (! empty($webhook['token'])) {
                    $auth = new Token($webhook['token']);
                } elseif (! empty($webhook['username']) && ! empty($webhook['password'])) {
                    $auth = new User($webhook['username'], $webhook['password']);
                }

                // Create a client with optional authentication
                $client = new Client($server, $auth);
                $response = $client->send($message);

                Notification::make()
                    ->title("Test ntfy notification sent to {$webhook['topic']}.")
                    ->success()
                    ->send();

            } catch (EndpointException|NtfyException $err) {
                Notification::make()
                    ->title("Failed to send notification to {$webhook['topic']}.")
                    ->warning()
                    ->send();
            }
        }
    }
}
