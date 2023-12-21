<?php

namespace App\Console\Commands;

use App\Jobs\ExecSpeedtest;
use App\Settings\GeneralSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class RunOoklaSpeedtest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-ookla-speedtest
                            {--scheduled : Option used to determine if the command was run from the scheduler}
                            {server? : Specify an Ookla server to run the test against}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Run a speedtest against Ookla's service.";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $config = [];

        $settings = new GeneralSettings();

        if (is_array($settings->speedtest_server) && count($settings->speedtest_server)) {
            $config = array_merge($config, [
                'ookla_server_id' => Arr::random($settings->speedtest_server),
            ]);
        }

        if (! $this->option('scheduled')) {
            if ($this->argument('server')) {
                $config = array_merge($config, [
                    'ookla_server_id' => $this->argument('server'),
                ]);
            }

            try {
                ExecSpeedtest::dispatch(
                    speedtest: $config,
                    scheduled: false
                );
            } catch (\Throwable $th) {
                Log::warning($th);

                return Command::FAILURE;
            }

            return Command::SUCCESS;
        }

        try {
            ExecSpeedtest::dispatch(
                speedtest: $config,
                scheduled: true
            );
        } catch (\Throwable $th) {
            Log::warning($th);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
