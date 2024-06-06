<?php

use App\Actions\Speedtests\RunScheduledSpeedtests;
use Illuminate\Support\Facades\Schedule;

/**
 * Checks if Result model records should be pruned.
 */
if (config('speedtest.prune_results_older_than') > 0) {
    Schedule::command('model:prune', [
        '--model' => [\App\Models\Result::class],
    ])->daily();
}

/**
 * Checked for new versions weekly on Thursday because
 * I usually do releases on Thursday or Friday.
 */
Schedule::command('app:version')
    ->weeklyOn(5);

/**
 * Action to run scheduled speedtests.
 */
Schedule::call(fn () => RunScheduledSpeedtests::run())
    ->everyMinute()
    ->when(! blank(config('speedtest.schedule')));
