<?php

namespace App\Jobs;

use App\Models\Result;
use App\Models\Speedtest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ExecSpeedtest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public array|null $speedtest = null
    ) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $args = [
            'speedtest',
            '--accept-license',
            '--format=json',
        ];

        if (! blank($this->speedtest)) {
            if (! blank($this->speedtest['ookla_server_id'])) {
                $args = array_merge($args, ['--server-id='.$this->speedtest['ookla_server_id']]);
            }
        }

        $process = new Process($args);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);

            return 0;
        }

        $output = $process->getOutput();

        Result::create([
            'data' => $output,
        ]);

        return 0;
    }
}
