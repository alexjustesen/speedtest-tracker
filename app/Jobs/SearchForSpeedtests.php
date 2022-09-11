<?php

namespace App\Jobs;

use App\Models\Speedtest;
use Cron\CronExpression;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SearchForSpeedtests implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $tests = Speedtest::query()
            ->where('next_run_at', now()->format('Y-m-d H:i'))
            ->get();

        foreach ($tests as $item) {
            ExecSpeedtest::dispatch(speedtest: $item);

            $cron = new CronExpression($item->schedule);

            $item->next_run_at = $cron->getNextRunDate()->format('Y-m-d H:i');
            $item->save();
        }

        Log::info($tests);
    }
}
