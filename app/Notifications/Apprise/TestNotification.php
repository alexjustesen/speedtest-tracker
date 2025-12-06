<?php

namespace App\Notifications\Apprise;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TestNotification extends Notification implements ShouldQueue
{
    use Queueable;

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
            ->title('Test Notification')
            ->body('👋 Testing the Apprise notification channel.')
            ->type('info');
    }
}
