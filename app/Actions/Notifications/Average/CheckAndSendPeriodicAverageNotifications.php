<?php

namespace App\Actions\Notifications\Average;

use App\Enums\ReportPeriod;
use App\Jobs\Notifications\SendPeriodicAverageReportJob;

class CheckAndSendPeriodicAverageNotifications
{
    public static function run(ReportPeriod $period): void
    {
        SendPeriodicAverageReportJob::dispatch($period);
    }
}
