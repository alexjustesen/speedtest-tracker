<?php

namespace App\Notifications\Telegram;

use App\Settings\NotificationSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class TestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $settings;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->settings = new NotificationSettings;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['telegram'];
    }

    /**
     * Get the Telegram representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toTelegram(object $notifiable): TelegramMessage
    {
        return TelegramMessage::create()
            ->to($notifiable->routes['telegram_chat_id'])
            ->disableNotification($this->settings->telegram_disable_notification)
            ->content('👋 Testing the Telegram notification channel.');
    }
}
