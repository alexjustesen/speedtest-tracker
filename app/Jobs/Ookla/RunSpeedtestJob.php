<?php

namespace App\Jobs\Ookla;

use App\Enums\ResultStatus;
use App\Events\SpeedtestFailed;
use App\Events\SpeedtestRunning;
use App\Helpers\Ookla;
use App\Models\Result;
use App\Models\Schedule;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Support\Arr;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class RunSpeedtestJob implements ShouldQueue
{
    use Batchable, Queueable;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    public $interface;

    public $result;

    /**
     * Create a new job instance.
     */
    public function __construct(Result $result, ?string $interface = null)
    {
        $this->result = $result;
        $this->interface = $interface; // Allow null interface
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [
            new SkipIfBatchCancelled,
        ];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->result->update([
            'status' => ResultStatus::Running,
        ]);

        SpeedtestRunning::dispatch($this->result);

        $command = array_filter([
            'speedtest',
            '--accept-license',
            '--accept-gdpr',
            '--format=json',
            $this->result->server_id ? '--server-id='.$this->result->server_id : null,
            $this->interface ? '--interface='.$this->interface : null,
        ]);

        $process = new Process($command);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $exception) {
            $this->result->update([
                'data->type' => 'log',
                'data->level' => 'error',
                'data->message' => Ookla::getErrorMessage($exception),
                'status' => ResultStatus::Failed,
            ]);

            $this->result->load('schedule');
            if ($schedule = $this->result->schedule) {
                $schedule->increment('failed_runs');
            }

            $this->batch()->cancel();

            SpeedtestFailed::dispatch($this->result);

            return;
        }

        $output = json_decode($process->getOutput(), true);

        $this->result->update([
            'ping' => Arr::get($output, 'ping.latency'),
            'download' => Arr::get($output, 'download.bandwidth'),
            'upload' => Arr::get($output, 'upload.bandwidth'),
            'data' => $output,
        ]);
    }
}
