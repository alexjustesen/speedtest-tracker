<?php

namespace App\Listeners\Threshold;

use App\Events\SpeedtestCompleted;
use App\Settings\GeneralSettings;
use App\Settings\NotificationSettings;
use App\Settings\ThresholdSettings;
use App\Telegram\TelegramNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
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
    public function handle(SpeedtestCompleted $event): void
    {
        if (! $this->thresholdSettings->absolute_enabled) {
            return;
        }

        // Telegram notification channel
        if ($this->notificationSettings->telegram_enabled == true && $this->notificationSettings->telegram_on_threshold_failure == true) {
            $this->telegramChannel($event);
        }

        // Discord notification channel
        if ($this->notificationSettings->discord_enabled == true && $this->notificationSettings->discord_on_threshold_failure == true) {
            $this->discordChannel($event);
        }
    }

    /**
     * Handle telegram notifications.
     */
    protected function telegramChannel(SpeedtestCompleted $event): void
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
    protected function discordChannel(SpeedtestCompleted $event): void
    {
        if ($this->notificationSettings->discord_enabled) {
            $failedThresholds = []; // Initialize an array to keep track of failed thresholds

            // Check Download threshold
            if ($this->thresholdSettings->absolute_download > 0 && absoluteDownloadThresholdFailed($this->thresholdSettings->absolute_download, $event->result->download)) {
                $failedThresholds['Download'] = toBits(convertSize($event->result->download), 2).' Mbps';
            }

            // Check Upload threshold
            if ($this->thresholdSettings->absolute_upload > 0 && absoluteUploadThresholdFailed($this->thresholdSettings->absolute_upload, $event->result->upload)) {
                $failedThresholds['Upload'] = toBits(convertSize($event->result->upload), 2).' Mbps';
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
}
