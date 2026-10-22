<?php

namespace App\Notifications\Drivers;

use App\Actions\Notifications\BuildNotificationMessage;
use App\Models\NotificationChannel;
use App\Notifications\NotificationMessage;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Auth;
use Spatie\WebhookServer\WebhookCall;

class WebhookDriver extends NotificationDriver
{
    public function configSchema(): array
    {
        return [
            TextInput::make('url')
                ->label(__('notification_channels.webhook_url'))
                ->placeholder('https://webhook.site/longstringofcharacters')
                ->url()
                ->rule('url:http,https')
                ->required()
                ->maxLength(2000),

            TextInput::make('secret')
                ->label(__('notification_channels.webhook_secret'))
                ->helperText(__('notification_channels.webhook_secret_helper'))
                ->password()
                ->revealable()
                ->maxLength(255),
        ];
    }

    /**
     * Queue the webhook call. Spatie retries failed calls itself, and the final
     * outcome is recorded on the channel by RecordWebhookDelivery.
     */
    public function send(NotificationChannel $channel, NotificationMessage $message): void
    {
        $this->call($channel, $message, [
            'notification_channel_id' => $channel->id,
        ])->dispatch();
    }

    /**
     * Queue a test call, reported back to the user by NotifyWebhookTestResult.
     */
    public function test(NotificationChannel $channel): void
    {
        $this->call($channel, BuildNotificationMessage::test(), [
            'webhook_test' => true,
            'user_id' => Auth::id(),
        ])->dispatch();
    }

    public function deliversAsynchronously(): bool
    {
        return true;
    }

    /**
     * Build the (optionally signed) webhook call.
     *
     * @param  array<string, mixed>  $meta
     */
    private function call(NotificationChannel $channel, NotificationMessage $message, array $meta): WebhookCall
    {
        $call = WebhookCall::create()
            ->url($channel->config('url'))
            ->payload($message->payload())
            ->meta($meta);

        $secret = $channel->config('secret');

        return filled($secret)
            ? $call->useSecret($secret)
            : $call->doNotSign();
    }
}
