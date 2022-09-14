<?php

namespace App\Jobs;

use Cron\CronExpression;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

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
        if (File::exists(base_path().'/config.yml')) {
            $config = Yaml::parseFile(
                base_path().'/config.yml'
            );
        }

        if (File::exists('/app/config.yml')) {
            $config = Yaml::parseFile('/app/config.yml');
        }

        $speedtest = $config['speedtest'];

        $cron = new CronExpression($speedtest['schedule']);

        if ($cron->isDue() && $speedtest['enabled']) {
            ExecSpeedtest::dispatch(speedtest: $speedtest, scheduled: true);
        }
    }
}
