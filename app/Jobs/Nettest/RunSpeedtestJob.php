<?php

namespace App\Jobs\Nettest;

use App\Enums\ResultStatus;
use App\Events\SpeedtestFailed;
use App\Events\SpeedtestRunning;
use App\Helpers\Nettest;
use App\Models\Result;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class RunSpeedtestJob implements ShouldQueue
{
    use Batchable, Queueable;

    /**
     * The number of seconds the job can run before timing out.
     *
     * Nettest runs a ping, a VoIP, a packet loss, a download and an upload
     * phase, so it needs more time than a plain download and upload test.
     *
     * @var int
     */
    public $timeout = 180;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Result $result,
    ) {}

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

        if (blank(config('speedtest.nettest.server'))) {
            $this->markAsFailed('No Nettest server is configured, set NETTEST_SERVER to the address of your Nettest server.');

            return;
        }

        $process = new Process(Nettest::getCommand());

        try {
            $process->mustRun();
        } catch (ProcessFailedException $exception) {
            $this->markAsFailed(Nettest::getErrorMessage($exception));

            return;
        }

        $output = json_decode($process->getOutput(), true);

        if (! is_array($output)) {
            $this->markAsFailed('The Nettest CLI did not return a valid JSON result.');

            return;
        }

        $this->result->update(Nettest::mapResult($output));
    }

    /**
     * Marks the result as failed and stops the rest of the batch.
     */
    protected function markAsFailed(string $message): void
    {
        $this->result->update([
            'data->type' => 'log',
            'data->level' => 'error',
            'data->message' => $message,
            'status' => ResultStatus::Failed,
        ]);

        $this->batch()->cancel();

        SpeedtestFailed::dispatch($this->result);
    }
}
