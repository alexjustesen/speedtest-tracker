<?php

namespace App\Console\Commands;

use App\Jobs\ExecSpeedtest;
use Illuminate\Console\Command;

class RunSpeedtest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:speedtest
                            {server? : Specify a server to run the test against}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a speedtest using Speedtest by Ookla';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $speedtest = [];

        if ($this->argument('server')) {
            $speedtest = array_merge($speedtest, ['ookla_server_id' => $this->argument('server')]);
        }

        ExecSpeedtest::dispatch(speedtest: $speedtest);

        $this->info('✅  added manual speedtest to the queue');

        return 0;
    }
}
