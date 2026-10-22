<?php

namespace App\Notifications\Drivers;

use App\Models\NotificationChannel;
use App\Notifications\NotificationMessage;
use App\Rules\AppriseScheme;
use App\Rules\ContainsString;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class AppriseDriver extends NotificationDriver
{
    public function configSchema(): array
    {
        return [
            TextInput::make('server_url')
                ->label(__('notification_channels.apprise_server_url'))
                ->placeholder('http://localhost:8000/notify')
                ->helperText(__('notification_channels.apprise_server_url_helper'))
                ->hintAction(
                    Action::make('apprise_docs')
                        ->label(__('general.view_documentation'))
                        ->icon('heroicon-m-arrow-top-right-on-square')
                        ->url('https://docs.speedtest-tracker.dev/settings/notifications/apprise')
                        ->openUrlInNewTab(),
                )
                ->maxLength(2000)
                ->required()
                ->url()
                ->rule('url:http,https')
                ->rule(new ContainsString('/notify')),

            Grid::make(2)
                ->schema([
                    TextInput::make('username')
                        ->label(__('notification_channels.apprise_username'))
                        ->placeholder(__('notification_channels.optional'))
                        ->maxLength(255),
                    TextInput::make('password')
                        ->label(__('notification_channels.apprise_password'))
                        ->placeholder(__('notification_channels.optional'))
                        ->password()
                        ->revealable()
                        ->maxLength(255),
                ]),

            Repeater::make('channel_urls')
                ->label(__('notification_channels.apprise_channels'))
                ->helperText(__('notification_channels.apprise_channels_helper'))
                ->simple(
                    TextInput::make('url')
                        ->placeholder('discord://WebhookID/WebhookToken')
                        ->maxLength(2000)
                        ->distinct()
                        ->required()
                        ->rule(new AppriseScheme),
                )
                ->defaultItems(0)
                ->requiredWithout('tags'),

            TagsInput::make('tags')
                ->label(__('notification_channels.apprise_tags'))
                ->helperText(__('notification_channels.apprise_tags_helper'))
                ->requiredWithout('channel_urls'),

            Toggle::make('verify_ssl')
                ->label(__('notification_channels.apprise_verify_ssl'))
                ->default(true),
        ];
    }

    public function send(NotificationChannel $channel, NotificationMessage $message): void
    {
        $serverUrl = rtrim((string) $channel->config('server_url'), '/');

        if (blank($serverUrl)) {
            throw new RuntimeException(__('notification_channels.errors.no_apprise_server'));
        }

        $urls = array_values(array_filter($channel->config('channel_urls', [])));
        $tags = array_values(array_filter($channel->config('tags', [])));

        if (empty($urls) && empty($tags)) {
            throw new RuntimeException(__('notification_channels.errors.no_apprise_targets'));
        }

        $request = Http::acceptJson()->timeout(30);

        if (filled($channel->config('username'))) {
            $request = $request->withBasicAuth($channel->config('username'), (string) $channel->config('password'));
        }

        // Fields added after the form was filled have no default, treat unset as enabled.
        if ($channel->config('verify_ssl') === false) {
            $request = $request->withoutVerifying();
        }

        try {
            $response = $request->post($serverUrl, array_filter([
                'urls' => empty($urls) ? null : implode(',', $urls),
                // A comma separated list matches any of the tags.
                'tag' => empty($tags) ? null : implode(',', $tags),
                'title' => $message->title,
                'body' => $message->bodyWithLinks(),
                'type' => $message->level->value,
                'format' => 'markdown',
            ]));
        } catch (ConnectionException $e) {
            Log::error('Apprise notification failed', [
                'channel' => $channel->id,
                'instance' => $serverUrl,
                'message' => $e->getMessage(),
            ]);

            throw new RuntimeException($this->connectionErrorMessage($e, $serverUrl), previous: $e);
        }

        // Only accept 200 OK responses as successful.
        if ($response->status() !== 200) {
            $error = $response->json('error') ?? $response->reason();

            throw new RuntimeException(__('notification_channels.errors.apprise_response', [
                'status' => $response->status(),
                'error' => $error,
            ]));
        }
    }

    /**
     * Turn a low level cURL error into something readable in the UI.
     */
    private function connectionErrorMessage(ConnectionException $e, string $serverUrl): string
    {
        $message = $e->getMessage();

        return match (true) {
            str_contains($message, 'Could not resolve host') => "Could not connect to Apprise server at {$serverUrl}",
            str_contains($message, 'Connection refused') => "Connection refused by Apprise server at {$serverUrl}",
            str_contains($message, 'timed out') => "Connection to Apprise server at {$serverUrl} timed out",
            default => "Failed to connect to Apprise server at {$serverUrl}",
        };
    }
}
