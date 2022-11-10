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
        $args = [
            'speedtest',
            '--accept-license',
            '--accept-gdpr',
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

        try {
            $output = $process->getOutput();
            $results = json_decode($output, true);

            Result::create([
                'ping' => $results['ping']['latency'],
                'jitter' => $results['ping']['jitter'],
                'download' => $results['download']['bandwidth'],
                'download_jitter' => $results['download']['latency']['jitter'],
                'upload' => $results['upload']['bandwidth'],
                'upload_jitter'  => $results['upload']['latency']['jitter'],
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
