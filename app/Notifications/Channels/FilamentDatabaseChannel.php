<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;

class FilamentDatabaseChannel
{
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): string
    {
        return 'I am the message string';
    }
}
