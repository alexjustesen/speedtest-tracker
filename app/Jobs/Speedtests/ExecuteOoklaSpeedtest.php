<?php

namespace App\Jobs\Speedtests;

use App\Enums\ResultStatus;
use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestFailed;
use App\Models\Result;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ExecuteOoklaSpeedtest implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Result $result,
        public ?int $serverId,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /**
         * Check to make sure there is an internet connection first.
         */
        try {
            Http::retry(3, 500)->get('1.1.1.1');
        } catch (\Throwable $th) {
            $this->result->update([
                'data' => [
                    'type' => 'log',
                    'level' => 'error',
                    'message' => 'Could not resolve host.',
                ],
                'status' => ResultStatus::Failed,
            ]);

            SpeedtestFailed::dispatch($this->result);

            return;
        }

        $options = array_filter([
            'speedtest',
            '--accept-license',
            '--accept-gdpr',
            '--format=json',
            optional($this->serverId) ? '--server-id='.$this->serverId : false,
        ]);

        $process = new Process($options);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $exception) {
            $messages = explode(PHP_EOL, $exception->getMessage());

            $message = collect(array_filter($messages, 'json_validate'))->last();

            $this->result->update([
                'data' => json_decode($message, true),
                'status' => ResultStatus::Failed,
            ]);

            SpeedtestFailed::dispatch($this->result);

            return;
        }

        $output = json_decode($process->getOutput(), true);

        $this->result->update([
            'ping' => Arr::get($output, 'ping.latency'),
            'download' => Arr::get($output, 'download.bandwidth'),
            'upload' => Arr::get($output, 'upload.bandwidth'),
            'data' => $output,
            'status' => ResultStatus::Completed,
        ]);

        SpeedtestCompleted::dispatch($this->result);
    }
}
