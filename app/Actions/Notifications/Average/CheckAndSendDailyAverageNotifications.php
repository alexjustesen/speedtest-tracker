<?php

namespace App\Actions\Notifications\Average;

use App\Jobs\Notifications\SendDailyAverageReportJob;
use App\Settings\NotificationSettings;

class CheckAndSendDailyAverageNotifications
{
    public static function run(): void
    {
        $notificationSettings = app(NotificationSettings::class);

        if ($notificationSettings->mail_daily_average_enabled || $notificationSettings->apprise_daily_average_enabled) {
            SendDailyAverageReportJob::dispatch();
        }
    }
}
