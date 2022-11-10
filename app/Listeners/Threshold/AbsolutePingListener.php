<?php

namespace App\Listeners\Threshold;

use App\Events\ResultCreated;
use App\Settings\NotificationSettings;
use App\Settings\ThresholdSettings;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AbsolutePingListener
{
    public $notificationSettings;

    public $thresholdSettings;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->notificationSettings = new (NotificationSettings::class);

        $this->thresholdSettings = new (ThresholdSettings::class);
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ResultCreated  $event
     * @return void
     */
    public function handle(ResultCreated $event)
    {
        if (! $this->thresholdSettings->absolute_enabled && ! $this->thresholdSettings->absolute_ping) {
            return;
        }

        if ($event->result->ping > $this->thresholdSettings->absolute_ping) {
            Notification::make()
                ->title('Threshold breached')
                ->body('Speedtest #'.$event->result->id.' breached the ping threshold of '.$this->thresholdSettings->absolute_ping.'Ms at '.$event->result->ping.'Ms.')
                ->warning()
                ->sendToDatabase($event->user);
        }
    }
}
