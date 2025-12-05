<?php

namespace App\Notifications\Apprise;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SpeedtestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $body,
        public string $type = 'info',
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['apprise'];
    }

    /**
     * Get the Apprise message representation of the notification.
     */
    public function toApprise(object $notifiable): AppriseMessage
    {
        return AppriseMessage::create()
            ->urls($notifiable->routes['apprise_urls'])
            ->title($this->title)
            ->body($this->body)
            ->type($this->type);
    }
}
