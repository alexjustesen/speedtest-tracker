<?php

namespace App\Listeners;

use App\Events\ResultCreated;
use App\Settings\NotificationSettings;
use Filament\Notifications\Notification;

class SpeedtestCompletedListener
{
    public $notificationSettings;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->notificationSettings = new (NotificationSettings::class);
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ResultCreated  $event
     * @return void
     */
    public function handle(ResultCreated $event)
    {
        if (! $this->notificationSettings->database_enabled) {
            return;
        }

        if ($this->notificationSettings->database_on_speedtest_run) {
            Notification::make()
                ->title('Speedtest completed')
                ->success()
                ->sendToDatabase($event->user);
        }
    }
}
