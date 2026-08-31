<?php

namespace App\Actions\Schedules;

use App\Actions\Ookla\RunSpeedtest;
use App\Models\Speedtest;
use Lorisleiva\Actions\Concerns\AsAction;

class RunScheduledSpeedtest
{
    use AsAction;

    public function handle(Speedtest $speedtest): mixed
    {
        return RunSpeedtest::run(scheduled: true, schedule: $speedtest);
    }
}
