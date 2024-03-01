<?php

namespace App\Listeners\Threshold;

use App\Events\SpeedtestCompleted;
use App\Settings\GeneralSettings;
use App\Settings\NotificationSettings;
use App\Settings\ThresholdSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
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

        // Discord notification channel
        if ($this->notificationSettings->discord_enabled == true && $this->notificationSettings->discord_on_threshold_failure == true) {
            $this->discordChannel($event);
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
