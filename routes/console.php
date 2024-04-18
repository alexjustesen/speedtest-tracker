<?php

use App\Actions\Speedtests\RunScheduledSpeedtests;
use App\Console\Commands\SystemMaintenance;
use App\Console\Commands\VersionChecker;
use App\Models\Result;
use App\Settings\GeneralSettings;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


$settings = new GeneralSettings();

/**
 * Checks if Result model records should be pruned.
 */
if ($settings->prune_results_older_than > 0) {
    Schedule::command('model:prune', [
        '--model' => [Result::class],
    ])->daily();
}

/**
 * Perform system maintenance weekly on Sunday morning,
 * start off the week nice and fresh.
 */
Schedule::command(SystemMaintenance::class)->weeklyOn(0)
    ->timezone($settings->timezone ?? 'UTC');

/**
 * Checked for new versions weekly on Thursday because
 * I usually do releases on Thursday or Friday.
 */
Schedule::command(VersionChecker::class)->weeklyOn(5)
    ->timezone($settings->timezone ?? 'UTC');

/**
 * Action to run scheduled speedtests.
 */
Schedule::call(function () {
    RunScheduledSpeedtests::run();
})
    ->everyMinute()
    ->when(function () use ($settings) {
        return ! blank($settings->speedtest_schedule);
    });
