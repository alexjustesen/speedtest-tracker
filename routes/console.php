<?php

use App\Actions\CheckForScheduledSpeedtests;
use App\Actions\Notifications\Average\CheckAndSendDailyAverageNotifications;
use App\Actions\Notifications\Average\CheckAndSendMonthlyAverageNotifications;
use App\Actions\Notifications\Average\CheckAndSendWeeklyAverageNotifications;
use Illuminate\Support\Facades\Schedule;

/**
 * Checks if Result model records should be pruned.
 */
Schedule::command('model:prune')
    ->daily()
    ->when(function () {
        return config('speedtest.prune_results_older_than') > 0;
    });

/**
 * Nightly maintenance
 */
Schedule::daily()
    ->group(function () {
        Schedule::command('queue:prune-batches --hours=48');
        Schedule::command('queue:prune-failed --hours=48');
    });

/**
 * Check for scheduled speedtests.
 */
Schedule::everyMinute()
    ->group(function () {
        Schedule::call(fn () => CheckForScheduledSpeedtests::run());
    });

/**
 * Send daily average report at 6 AM.
 */
// Schedule::dailyAt('00:05')
Schedule::everyMinute()
    ->group(function () {
        Schedule::call(fn () => CheckAndSendDailyAverageNotifications::run());
    });

/**
 * Send weekly average report every Monday at 6 AM.
 */
// Schedule::weeklyOn(1, '00:05')
Schedule::everyMinute()
    ->group(function () {
        Schedule::call(fn () => CheckAndSendWeeklyAverageNotifications::run());
    });

/**
 * Send monthly average report on the 1st of each month at 6 AM.
 */
// Schedule::monthlyOn(1, '00:05')
Schedule::everyMinute()
    ->group(function () {
        Schedule::call(fn () => CheckAndSendMonthlyAverageNotifications::run());
    });
