<?php

namespace App\Listeners;

use App\Actions\Ookla\RetrySpeedtest;
use App\Events\SpeedtestBenchmarkUnhealthy;
use Illuminate\Contracts\Queue\ShouldQueue;

class RetryUnhealthySpeedtest implements ShouldQueue
{
    /**
     * The number of seconds to delay the job.
     *
     * @var int
     */
    public $delay;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->delay = config('speedtest.retry_delay');
    }

    /**
     * Handle the event.
     */
    public function handle(SpeedtestBenchmarkUnhealthy $event): void
    {
        RetrySpeedtest::run($event->result, $event->attempt);
    }
}
