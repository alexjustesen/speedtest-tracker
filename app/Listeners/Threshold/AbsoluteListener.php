<?php

namespace App\Listeners\Threshold;

use App\Events\ResultCreated;
use App\Mail\Threshold\AbsoluteMail;
use App\Settings\NotificationSettings;
use App\Settings\ThresholdSettings;
use App\Telegram\TelegramNotification;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

        // Telegram notification channel
        if ($this->notificationSettings->telegram_enabled == true && $this->notificationSettings->telegram_on_threshold_failure == true) {
            $this->telegramChannel($event);
        }
    }

    /**
     * Handle database notifications.
     *
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
     * @return void
     */
    protected function mailChannel(ResultCreated $event)
    {
        $failedThresholds = [];

        if (! count($this->notificationSettings->mail_recipients) > 0) {
            Log::info('Skipping sending mail notification, no recipients.');
        }

        // Download threshold
        if ($this->thresholdSettings->absolute_download > 0) {
            if (absoluteDownloadThresholdFailed($this->thresholdSettings->absolute_download, $event->result->download)) {
                array_push($failedThresholds, [
                    'name' => 'Download',
                    'threshold' => $this->thresholdSettings->absolute_download.' Mbps',
                    'value' => formatBits(formatBytesToBits($event->result->download)).'ps',
                ]);
            }
        }

        // Upload threshold
        if ($this->thresholdSettings->absolute_upload > 0) {
            if (absoluteUploadThresholdFailed($this->thresholdSettings->absolute_upload, $event->result->upload)) {
                array_push($failedThresholds, [
                    'name' => 'Upload',
                    'threshold' => $this->thresholdSettings->absolute_upload.' Mbps',
                    'value' => formatBits(formatBytesToBits($event->result->upload)).'ps',
                ]);
            }
        }

        // Ping threshold
        if ($this->thresholdSettings->absolute_ping > 0) {
            if (absolutePingThresholdFailed($this->thresholdSettings->absolute_ping, $event->result->ping)) {
                array_push($failedThresholds, [
                    'name' => 'Ping',
                    'threshold' => $this->thresholdSettings->absolute_ping.' Ms',
                    'value' => round($event->result->ping, 2).' Ms',
                ]);
            }
        }

        if (count($failedThresholds)) {
            foreach ($this->notificationSettings->mail_recipients as $recipient) {
                Mail::to($recipient)
                    ->send(new AbsoluteMail($event->result, $failedThresholds));
            }
        }
    }

    /**
     * Handle telegram notifications.
     *
     * @return void
     */
    protected function telegramChannel(ResultCreated $event)
    {
        $failedThresholds = [];

        if (! count($this->notificationSettings->telegram_recipients) > 0) {
            Log::info('Skipping sending telegram notification, no recipients.');
        }

        // Download threshold
        if ($this->thresholdSettings->absolute_download > 0) {
            if (absoluteDownloadThresholdFailed($this->thresholdSettings->absolute_download, $event->result->download)) {
                array_push($failedThresholds, [
                    'name' => 'Download',
                    'threshold' => $this->thresholdSettings->absolute_download.' Mbps',
                    'value' => formatBits(formatBytesToBits($event->result->download)).'ps',
                ]);
            }
        }

        // Upload threshold
        if ($this->thresholdSettings->absolute_upload > 0) {
            if (absoluteUploadThresholdFailed($this->thresholdSettings->absolute_upload, $event->result->upload)) {
                array_push($failedThresholds, [
                    'name' => 'Upload',
                    'threshold' => $this->thresholdSettings->absolute_upload.' Mbps',
                    'value' => formatBits(formatBytesToBits($event->result->upload)).'ps',
                ]);
            }
        }

        // Ping threshold
        if ($this->thresholdSettings->absolute_ping > 0) {
            if (absolutePingThresholdFailed($this->thresholdSettings->absolute_ping, $event->result->ping)) {
                array_push($failedThresholds, [
                    'name' => 'Ping',
                    'threshold' => $this->thresholdSettings->absolute_ping.' Ms',
                    'value' => round($event->result->ping, 2).' Ms',
                ]);
            }
        }

        if (count($failedThresholds)) {
            foreach ($this->notificationSettings->telegram_recipients as $recipient) {
                $message = view('telegram.threshold.absolute', [
                    'id' => $event->result->id,
                    'url' => url('/admin/results'),
                    'metrics' => $failedThresholds,
                ])->render();

                \Illuminate\Support\Facades\Notification::route('telegram_chat_id', $recipient['telegram_chat_id'])
                        ->notify(new TelegramNotification($message));
            }
        }
    }
}
