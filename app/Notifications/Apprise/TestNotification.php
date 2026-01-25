<?php

namespace App\Notifications\Apprise;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     * Set to 1 to prevent duplicate notifications.
     * Apprise may take >30s to respond (timeout), but still processes successfully.
     * See #2653 and #2615
     *
     * @var int
     */
    public $tries = 1;

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
        $body = '👋 This is a test notification from **'.config('app.name')."**.\n\n";
        $body .= "If you're seeing this, your Apprise notification channel is configured correctly!\n\n";

        return AppriseMessage::create()
            ->urls($notifiable->routes['apprise_urls'])
            ->title('Test Notification')
            ->body($body)
            ->type('info')
            ->format('markdown');
    }
}
