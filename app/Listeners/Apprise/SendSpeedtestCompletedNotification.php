<?php

namespace App\Listeners\Apprise;

use App\Events\SpeedtestCompleted;
use App\Notifications\Apprise\SpeedtestNotification;
use App\Services\Notifications\SpeedtestNotificationData;
use App\Settings\NotificationSettings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendSpeedtestCompletedNotification
{
    /**
     * Handle the event.
     */
    public function handle(SpeedtestCompleted $event): void
    {
        $notificationSettings = new NotificationSettings;

        if (! $notificationSettings->apprise_enabled) {
            return;
        }

        if (! $notificationSettings->apprise_on_speedtest_run) {
            return;
        }

        if (empty($notificationSettings->apprise_channel_urls) || ! is_array($notificationSettings->apprise_channel_urls)) {
            Log::warning('Apprise service URLs not found; check Apprise notification settings.');

            return;
        }

        // Build the speedtest data
        $data = SpeedtestNotificationData::make($event->result);

        $body = view('apprise.speedtest-completed', $data)->render();
        $title = 'Speedtest Completed – #'.$event->result->id;

        // Send notification to each configured channel URL
        foreach ($notificationSettings->apprise_channel_urls as $row) {
            $channelUrl = $row['channel_url'] ?? null;
            if (! $channelUrl) {
                Log::warning('Skipping entry with missing channel_url.');

                continue;
            }

            Notification::route('apprise_urls', $channelUrl)
                ->notify(new SpeedtestNotification($title, $body, 'info'));
        }
    }
}
