<?php

namespace App\Notifications\Database;

use App\Notifications\Channels\FilamentDatabaseChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SendSpeedtestCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [
            FilamentDatabaseChannel::class,
        ];
    }
}
