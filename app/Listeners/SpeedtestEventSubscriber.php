<?php

namespace App\Listeners;

use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestFailed;
use App\Jobs\Influxdb\v2\WriteResult;
use App\Jobs\Notifications\Apprise\SendSpeedtestCompletedNotification as AppriseCompleted;
use App\Jobs\Notifications\Apprise\SendSpeedtestThresholdNotification as AppriseThresholds;
use App\Jobs\Notifications\Database\SendSpeedtestCompletedNotification as DatabaseCompleted;
use App\Jobs\Notifications\Database\SendSpeedtestThresholdNotification as DatabaseThresholds;
use App\Jobs\Notifications\Mail\SendSpeedtestCompletedNotification as MailCompleted;
use App\Jobs\Notifications\Mail\SendSpeedtestThresholdNotification as MailThresholds;
use App\Jobs\Notifications\Webhook\SendSpeedtestCompletedNotification as WebhookCompleted;
use App\Jobs\Notifications\Webhook\SendSpeedtestThresholdNotification as WebhookThresholds;
use App\Settings\DataIntegrationSettings;
use App\Settings\NotificationSettings;
use Illuminate\Events\Dispatcher;

class SpeedtestEventSubscriber
{
    /**
     * Handle speedtest failed events.
     */
    public function handleSpeedtestFailed(SpeedtestFailed $event): void
    {
        // Handle failed event if necessary
    }

    /**
     * Handle speedtest completed events.
     */
    public function handleSpeedtestCompleted(SpeedtestCompleted $event): void
    {
        $settings = app(DataIntegrationSettings::class);

        // Write to InfluxDB if enabled
        if ($settings->influxdb_v2_enabled) {
            WriteResult::dispatch($event->result);
        }

        $notificationSettings = app(NotificationSettings::class);

        // Send Apprise notification if the setting is enabled
        if ($notificationSettings->apprise_enabled && $notificationSettings->apprise_on_speedtest_run) {
            AppriseCompleted::dispatch($event->result);
        }

        // Send Databse notification if the setting is enabled
        if ($notificationSettings->database_enabled && $notificationSettings->database_on_speedtest_run) {
            DatabaseCompleted::dispatch($event->result);
        }

        // Send Webhook notification if the setting is enabled
        if ($notificationSettings->webhook_enabled && $notificationSettings->webhook_on_speedtest_run) {
            WebhookCompleted::dispatch($event->result);
        }

        // Send Mail notification if the setting is enabled
        if ($notificationSettings->mail_enabled && $notificationSettings->mail_on_speedtest_run) {
            MailCompleted::dispatch($event->result);
        }

        // Send Apprise threshold failure notification if the setting is enabled
        if ($notificationSettings->apprise_enabled && $notificationSettings->apprise_on_threshold_failure) {
            AppriseThresholds::dispatch($event->result);
        }

        // Send Database threshold failure notification if the setting is enabled
        if ($notificationSettings->database_enabled && $notificationSettings->database_on_threshold_failure) {
            DatabaseThresholds::dispatch($event->result);
        }

        // Send Webhook threshold failure notification if the setting is enabled
        if ($notificationSettings->webhook_enabled && $notificationSettings->webhook_on_threshold_failure) {
            WebhookThresholds::dispatch($event->result);
        }

        // Send Mail threshold failure notification if the setting is enabled
        if ($notificationSettings->mail_enabled && $notificationSettings->mail_on_threshold_failure) {
            MailThresholds::dispatch($event->result);
        }
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            SpeedtestFailed::class,
            [SpeedtestEventSubscriber::class, 'handleSpeedtestFailed']
        );

        $events->listen(
            SpeedtestCompleted::class,
            [SpeedtestEventSubscriber::class, 'handleSpeedtestCompleted']
        );
    }
}
