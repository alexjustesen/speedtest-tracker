<?php

namespace App\Listeners\Threshold;

use App\Events\ResultCreated;
use App\Settings\NotificationSettings;
use App\Settings\ThresholdSettings;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class AbsoluteListener implements ShouldQueue
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
        if ($this->thresholdSettings->absolute_enabled !== true) {
            Log::info('Absolute threshold notifications disabled.');

            return;
        }

        // Database notification channel
        if ($this->notificationSettings->database_enabled == true && $this->notificationSettings->database_on_threshold_failure == true) {
            $this->databaseChannel($event);
        }
    }

    /**
     * Handle database notifications.
     *
     * @param  \App\Events\ResultCreated  $event
     * @return void
     */
    protected function databaseChannel(ResultCreated $event)
    {
        // Download threshold
        if ($this->thresholdSettings->absolute_download > 0) {
            if (formatBits(formatBytesToBits($event->result->download), 2, false) < $this->thresholdSettings->absolute_download) {
                Notification::make()
                    ->title('Threshold breached')
                    ->body('Speedtest #'.$event->result->id.' breached the download threshold of '.$this->thresholdSettings->absolute_download.'Mbps at '.formatBits(formatBytesToBits($event->result->download), 2, false).'Mbps.')
                    ->warning()
                    ->sendToDatabase($event->user);
            }
        } else {
            Log::info('Database absolute download threshold notification disabled.');
        }

        // Upload threshold
        if ($this->thresholdSettings->absolute_upload > 0) {
            if (formatBits(formatBytesToBits($event->result->upload), 2, false) < $this->thresholdSettings->absolute_upload) {
                Notification::make()
                    ->title('Threshold breached')
                    ->body('Speedtest #'.$event->result->id.' breached the upload threshold of '.$this->thresholdSettings->absolute_upload.'Mbps at '.formatBits(formatBytesToBits($event->result->upload), 2, false).'Mbps.')
                    ->warning()
                    ->sendToDatabase($event->user);
            }
        } else {
            Log::info('Database absolute upload threshold notification disabled.');
        }

        // Ping threshold
        if ($this->thresholdSettings->absolute_ping > 0) {
            if ($event->result->ping > $this->thresholdSettings->absolute_ping) {
                Notification::make()
                    ->title('Threshold breached')
                    ->body('Speedtest #'.$event->result->id.' breached the ping threshold of '.$this->thresholdSettings->absolute_ping.'ms at '.$event->result->ping.'ms.')
                    ->warning()
                    ->sendToDatabase($event->user);
            }
        } else {
            Log::info('Database absolute ping threshold notification disabled.');
        }
    }
}
