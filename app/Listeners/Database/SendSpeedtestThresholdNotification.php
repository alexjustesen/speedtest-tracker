<?php

namespace App\Listeners\Database;

use App\Events\SpeedtestCompleted;
use App\Helpers\Number;
use App\Models\User;
use App\Settings\NotificationSettings;
use App\Settings\ThresholdSettings;
use Filament\Notifications\Notification;

class SendSpeedtestThresholdNotification
{
    /**
     * Handle the event.
     */
    public function handle(SpeedtestCompleted $event): void
    {
        $notificationSettings = new NotificationSettings;

        if (! $notificationSettings->database_enabled) {
            return;
        }

        if (! $notificationSettings->database_on_threshold_failure) {
            return;
        }

        $thresholdSettings = new ThresholdSettings;

        if (! $thresholdSettings->absolute_enabled) {
            return;
        }

        if ($thresholdSettings->absolute_download > 0) {
            $this->absoluteDownloadThreshold(event: $event, thresholdSettings: $thresholdSettings);
        }

        if ($thresholdSettings->absolute_upload > 0) {
            $this->absoluteUploadThreshold(event: $event, thresholdSettings: $thresholdSettings);
        }

        if ($thresholdSettings->absolute_ping > 0) {
            $this->absolutePingThreshold(event: $event, thresholdSettings: $thresholdSettings);
        }
    }

    /**
     * Send database notification if absolute download threshold is breached.
     */
    protected function absoluteDownloadThreshold(SpeedtestCompleted $event, ThresholdSettings $thresholdSettings): void
    {
        if (! absoluteDownloadThresholdFailed($thresholdSettings->absolute_download, $event->result->download)) {
            return;
        }

        foreach (User::all() as $user) {
            Notification::make()
                ->title('Download threshold breached!')
                ->body('Speedtest #'.$event->result->id.' breached the download threshold of '.$thresholdSettings->absolute_download.' Mbps at '.Number::toBitRate($event->result->download_bits).'.')
                ->warning()
                ->sendToDatabase($user);
        }
    }

    /**
     * Send database notification if absolute upload threshold is breached.
     */
    protected function absoluteUploadThreshold(SpeedtestCompleted $event, ThresholdSettings $thresholdSettings): void
    {
        if (! absoluteUploadThresholdFailed($thresholdSettings->absolute_upload, $event->result->upload)) {
            return;
        }

        foreach (User::all() as $user) {
            Notification::make()
                ->title('Upload threshold breached!')
                ->body('Speedtest #'.$event->result->id.' breached the upload threshold of '.$thresholdSettings->absolute_upload.' Mbps at '.Number::toBitRate($event->result->upload_bits).'.')
                ->warning()
                ->sendToDatabase($user);
        }
    }

    /**
     * Send database notification if absolute upload threshold is breached.
     */
    protected function absolutePingThreshold(SpeedtestCompleted $event, ThresholdSettings $thresholdSettings): void
    {
        if (! absolutePingThresholdFailed($thresholdSettings->absolute_ping, $event->result->ping)) {
            return;
        }

        foreach (User::all() as $user) {
            Notification::make()
                ->title('Ping threshold breached!')
                ->body('Speedtest #'.$event->result->id.' breached the ping threshold of '.$thresholdSettings->absolute_ping.'ms at '.$event->result->ping.'ms.')
                ->warning()
                ->sendToDatabase($user);
        }
    }
}
