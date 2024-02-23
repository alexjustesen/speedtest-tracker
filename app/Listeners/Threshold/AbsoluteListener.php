<?php

namespace App\Listeners\Threshold;

use App\Events\ResultCreated;
use App\Mail\Threshold\AbsoluteMail;
use App\Settings\GeneralSettings;
use App\Settings\NotificationSettings;
use App\Settings\ThresholdSettings;
use App\Telegram\TelegramNotification;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\WebhookServer\WebhookCall;

class AbsoluteListener implements ShouldQueue
{
    public $generalSettings;

    public $notificationSettings;

    public $thresholdSettings;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->generalSettings = new (GeneralSettings::class);

        $this->notificationSettings = new (NotificationSettings::class);

        $this->thresholdSettings = new (ThresholdSettings::class);
    }

    /**
     * Handle the event.
     */
    public function handle(ResultCreated $event): void
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

        // Discord notification channel
        if ($this->notificationSettings->discord_enabled == true && $this->notificationSettings->discord_on_threshold_failure == true) {
            $this->discordChannel($event);
        }

        // Webhook notification channel
        if ($this->notificationSettings->webhook_enabled == true && $this->notificationSettings->webhook_on_threshold_failure == true) {
            $this->webhookChannel($event);
        }
    }

    /**
     * Handle database notifications.
     */
    protected function databaseChannel(ResultCreated $event): void
    {
        // Download threshold
        if ($this->thresholdSettings->absolute_download > 0) {
            if (absoluteDownloadThresholdFailed($this->thresholdSettings->absolute_download, $event->result->download)) {
                Notification::make()
                    ->title('Threshold breached')
                    ->body('Speedtest #'.$event->result->id.' breached the download threshold of '.$this->thresholdSettings->absolute_download.'Mbps at '.toBits(convertSize($event->result->download), 2).'Mbps.')
                    ->warning()
                    ->sendToDatabase($event->user);
            }
        }

        // Upload threshold
        if ($this->thresholdSettings->absolute_upload > 0) {
            if (absoluteUploadThresholdFailed($this->thresholdSettings->absolute_upload, $event->result->upload)) {
                Notification::make()
                    ->title('Threshold breached')
                    ->body('Speedtest #'.$event->result->id.' breached the upload threshold of '.$this->thresholdSettings->absolute_upload.'Mbps at '.toBits(convertSize($event->result->upload), 2).'Mbps.')
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
     */
    protected function mailChannel(ResultCreated $event): void
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
                    'value' => toBits(convertSize($event->result->download), 2).' Mbps',
                ]);
            }
        }

        // Upload threshold
        if ($this->thresholdSettings->absolute_upload > 0) {
            if (absoluteUploadThresholdFailed($this->thresholdSettings->absolute_upload, $event->result->upload)) {
                array_push($failedThresholds, [
                    'name' => 'Upload',
                    'threshold' => $this->thresholdSettings->absolute_upload.' Mbps',
                    'value' => toBits(convertSize($event->result->upload), 2).'Mbps',
                ]);
            }
        }

        // Ping threshold
        if ($this->thresholdSettings->absolute_ping > 0) {
            if (absolutePingThresholdFailed($this->thresholdSettings->absolute_ping, $event->result->ping)) {
                array_push($failedThresholds, [
                    'name' => 'Ping',
                    'threshold' => $this->thresholdSettings->absolute_ping.' ms',
                    'value' => round($event->result->ping, 2).' ms',
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
     */
    protected function telegramChannel(ResultCreated $event): void
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
                    'value' => toBits(convertSize($event->result->download), 2).'Mbps',
                ]);
            }
        }

        // Upload threshold
        if ($this->thresholdSettings->absolute_upload > 0) {
            if (absoluteUploadThresholdFailed($this->thresholdSettings->absolute_upload, $event->result->upload)) {
                array_push($failedThresholds, [
                    'name' => 'Upload',
                    'threshold' => $this->thresholdSettings->absolute_upload.' Mbps',
                    'value' => toBits(convertSize($event->result->upload), 2).'Mbps',
                ]);
            }
        }

        // Ping threshold
        if ($this->thresholdSettings->absolute_ping > 0) {
            if (absolutePingThresholdFailed($this->thresholdSettings->absolute_ping, $event->result->ping)) {
                array_push($failedThresholds, [
                    'name' => 'Ping',
                    'threshold' => $this->thresholdSettings->absolute_ping.' ms',
                    'value' => round($event->result->ping, 2).' ms',
                ]);
            }
        }

        if (count($failedThresholds)) {
            foreach ($this->notificationSettings->telegram_recipients as $recipient) {
                $message = view('telegram.threshold.absolute', [
                    'id' => $event->result->id,
                    'url' => url('/admin/results'),
                    'site_name' => $this->generalSettings->site_name,
                    'metrics' => $failedThresholds,
                ])->render();

                \Illuminate\Support\Facades\Notification::route('telegram_chat_id', $recipient['telegram_chat_id'])
                    ->notify(new TelegramNotification($message));
            }
        }
    }

    /**
     * Handle Discord notifications.
     */
    protected function discordChannel(ResultCreated $event): void
    {
        if ($this->notificationSettings->discord_enabled) {
            $failedThresholds = []; // Initialize an array to keep track of failed thresholds

            // Check Download threshold
            if ($this->thresholdSettings->absolute_download > 0 && absoluteDownloadThresholdFailed($this->thresholdSettings->absolute_download, $event->result->downloadBits)) {
                $failedThresholds['Download'] = ($event->result->downloadBits / 1000000).' (Mbps)';
            }

            // Check Upload threshold
            if ($this->thresholdSettings->absolute_upload > 0 && absoluteUploadThresholdFailed($this->thresholdSettings->absolute_upload, $event->result->uploadBits)) {
                $failedThresholds['Upload'] = ($event->result->uploadBits / 1000000).' (Mbps)';
            }

            // Check Ping threshold
            if ($this->thresholdSettings->absolute_ping > 0 && absolutePingThresholdFailed($this->thresholdSettings->absolute_ping, $event->result->ping)) {
                $failedThresholds['Ping'] = $event->result->ping.' ms';
            }

            // Proceed with sending notifications only if there are any failed thresholds
            if (count($failedThresholds) > 0) {
                if ($this->notificationSettings->discord_on_threshold_failure && count($this->notificationSettings->discord_webhooks)) {
                    foreach ($this->notificationSettings->discord_webhooks as $webhook) {
                        // Construct the payload with the failed thresholds information
                        $contentLines = [
                            'Result ID: '.$event->result->id,
                            'Site Name: '.$this->generalSettings->site_name,
                        ];

                        foreach ($failedThresholds as $metric => $result) {
                            $contentLines[] = "{$metric} threshold failed with result: {$result}.";
                        }

                        $payload = [
                            'content' => implode("\n", $contentLines),
                        ];

                        // Send the payload using WebhookCall
                        WebhookCall::create()
                            ->url($webhook['url'])
                            ->payload($payload)
                            ->doNotSign()
                            ->dispatch();

                    }
                }
            }
        }
    }

    /**
     * Handle webhook notifications.
     *
     * TODO: refactor
     */
    protected function webhookChannel(ResultCreated $event): void
    {
        $failedThresholds = [];

        if (! count($this->notificationSettings->webhook_urls) > 0) {
            Log::info('Skipping sending webhook notification, no urls.');
        }

        // Download threshold
        if ($this->thresholdSettings->absolute_download > 0) {
            if (absoluteDownloadThresholdFailed($this->thresholdSettings->absolute_download, $event->result->download)) {
                array_push($failedThresholds, [
                    'name' => 'Download',
                    'threshold' => $this->thresholdSettings->absolute_download,
                    'value' => toBits(convertSize($event->result->download), 2),
                ]);
            }
        }

        // Upload threshold
        if ($this->thresholdSettings->absolute_upload > 0) {
            if (absoluteUploadThresholdFailed($this->thresholdSettings->absolute_upload, $event->result->upload)) {
                array_push($failedThresholds, [
                    'name' => 'Upload',
                    'threshold' => $this->thresholdSettings->absolute_upload,
                    'value' => toBits(convertSize($event->result->upload), 2),
                ]);
            }
        }

        // Ping threshold
        if ($this->thresholdSettings->absolute_ping > 0) {
            if (absolutePingThresholdFailed($this->thresholdSettings->absolute_ping, $event->result->ping)) {
                array_push($failedThresholds, [
                    'name' => 'Ping',
                    'threshold' => $this->thresholdSettings->absolute_ping,
                    'value' => round($event->result->ping, 2),
                ]);
            }
        }

        if (count($failedThresholds)) {
            foreach ($this->notificationSettings->webhook_urls as $url) {
                Http::post($url['url'], [
                    'result_id' => $event->result->id,
                    'site_name' => $this->generalSettings->site_name,
                    'metrics' => $failedThresholds,
                ]);

                WebhookCall::create()
                    ->url($url['url'])
                    ->payload([
                        'result_id' => $event->result->id,
                        'site_name' => $this->generalSettings->site_name,
                        'metrics' => $failedThresholds,
                    ])
                    ->doNotSign()
                    ->dispatch();
            }
        }
    }
}
