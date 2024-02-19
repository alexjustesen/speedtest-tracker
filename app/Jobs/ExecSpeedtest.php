<?php

namespace App\Jobs;

use App\Enums\ResultStatus;
use App\Models\Result;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
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
        public ?array $speedtest = null,
        public bool $scheduled = false
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
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
                'service' => 'ookla',
                'data' => json_decode($message, true),
                'status' => ResultStatus::Failed,
                'scheduled' => $this->scheduled,
            ]);

            return;
        }

        try {
            $results = json_decode($process->getOutput(), true);

            Result::create([
                'service' => 'ookla',
                'ping' => Arr::get($results, 'ping.latency'),
                'download' => Arr::get($results, 'download.bandwidth'),
                'upload' => Arr::get($results, 'upload.bandwidth'),
                'data' => $results,
                'status' => ResultStatus::Completed,
                'scheduled' => $this->scheduled,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
