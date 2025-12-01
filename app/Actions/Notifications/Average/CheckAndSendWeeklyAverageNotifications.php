<?php

namespace App\Actions\Notifications\Average;

use App\Jobs\Notifications\SendWeeklyAverageReportJob;
use App\Settings\NotificationSettings;

class CheckAndSendWeeklyAverageNotifications
{
    public static function run(): void
    {
        $notificationSettings = app(NotificationSettings::class);

        if ($notificationSettings->mail_weekly_average_enabled) {
            SendWeeklyAverageReportJob::dispatch();
        }
    }
}
