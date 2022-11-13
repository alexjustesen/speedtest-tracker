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

        // Mail notification channel
        if ($this->notificationSettings->mail_enabled == true && $this->notificationSettings->mail_on_threshold_failure == true) {
            $this->mailChannel($event);
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
            if (absoluteDownloadThresholdFailed($this->thresholdSettings->absolute_download, $event->result->download)) {
                Notification::make()
                    ->title('Threshold breached')
                    ->body('Speedtest #'.$event->result->id.' breached the download threshold of '.$this->thresholdSettings->absolute_download.'Mbps at '.formatBits(formatBytesToBits($event->result->download), 2, false).'Mbps.')
                    ->warning()
                    ->sendToDatabase($event->user);
            }
        }

        // Upload threshold
        if ($this->thresholdSettings->absolute_upload > 0) {
            if (absoluteUploadThresholdFailed($this->thresholdSettings->absolute_upload, $event->result->upload)) {
                Notification::make()
                    ->title('Threshold breached')
                    ->body('Speedtest #'.$event->result->id.' breached the upload threshold of '.$this->thresholdSettings->absolute_upload.'Mbps at '.formatBits(formatBytesToBits($event->result->upload), 2, false).'Mbps.')
                    ->warning()
                    ->sendToDatabase($event->user);
            }
        }

        // Ping threshold
        if ($this->thresholdSettings->absolute_ping > 0) {
            if (absolutePingThresholdFailed($this->thresholdSettings->absolute_ping, $event->result->ping)) {
                Notification::make()
                    ->title('Threshold breached')
                    ->body('Speedtest #'.$event->result->id.' breached the ping threshold of '.$this->thresholdSettings->absolute_ping.'ms at '.$event->result->ping.'ms.')
                    ->warning()
                    ->sendToDatabase($event->user);
            }
        }
    }

    /**
     * Handle database notifications.
     *
     * @param  \App\Events\ResultCreated  $event
     * @return void
     */
    protected function mailChannel(ResultCreated $event)
    {

    }
}
