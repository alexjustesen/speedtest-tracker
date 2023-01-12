<?php

namespace App\Jobs;

use App\Models\Result;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ExecSpeedtest implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public array|null $speedtest = null,
        public bool $scheduled = false
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $process = new Process(
            array_filter([
                'speedtest',
                '--accept-license',
                '--accept-gdpr',
                '--format=json',
                optional($this->speedtest)['ookla_server_id'] ? '--server-id='.$this->speedtest['ookla_server_id'] : false,
            ])
        );

        try {
            $process->mustRun();
        } catch (ProcessFailedException $exception) {
            $messages = explode(PHP_EOL, $exception->getMessage());

            $message = collect(array_filter($messages, 'json_validate'))->last();

            Result::create([
                'scheduled' => $this->scheduled,
                'successful' => false,
                'data' => $message,
            ]);

            return 0;
        }

        try {
            $output = $process->getOutput();
            $results = json_decode($output, true);

            Result::create([
                'ping' => $results['ping']['latency'],
                'download' => $results['download']['bandwidth'],
                'upload' => $results['upload']['bandwidth'],
                'server_id' => $results['server']['id'],
                'server_name' => $results['server']['name'],
                'server_host' => $results['server']['host'].':'.$results['server']['port'],
                'url' => $results['result']['url'],
                'scheduled' => $this->scheduled,
                'data' => $output,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return 0;
    }
}
