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
use Illuminate\Support\Facades\URL;
use JJG\Ping;
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
        public ?int $serverId = null,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (! $this->checkForInternetConnection()) {
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

            // Extract only the "message" part from each JSON error message
            $errorMessages = array_map(function ($message) {
                $decoded = json_decode($message, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($decoded['message'])) {
                    return $decoded['message'];
                }

                return ''; // If it's not valid JSON or doesn't contain "message", return an empty string
            }, $messages);

            // Filter out empty messages and concatenate
            $errorMessage = implode(' | ', array_filter($errorMessages));

            // Prepare the error message data
            $data = [
                'type' => 'log',
                'level' => 'error',
                'message' => $errorMessage,
            ];

            // Add server ID if it exists
            if ($this->serverId !== null) {
                $data['server'] = ['id' => $this->serverId];
            }

            // Update the result with the error data
            $this->result->update([
                'data' => $data,
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

    /**
     * Check for internet connection.
     */
    protected function checkForInternetConnection(): bool
    {
        $url = config('speedtest.ping_url');

        // TODO: skip checking for internet connection, current validation does not take into account different host formats and ip addresses.
        return true;

        // Skip checking for internet connection if ping url isn't set (disabled)
        if (blank($url)) {
            return true;
        }

        if (! URL::isValidUrl($url)) {
            $this->result->update([
                'data' => [
                    'type' => 'log',
                    'level' => 'error',
                    'message' => 'Invalid ping URL.',
                ],
                'status' => ResultStatus::Failed,
            ]);

            SpeedtestFailed::dispatch($this->result);

            return false;
        }

        // Remove http:// or https:// from the URL if present
        $url = preg_replace('/^https?:\/\//', '', $url);

        $ping = new Ping(
            host: $url,
            timeout: 3,
        );

        if ($ping->ping() === false) {
            $this->result->update([
                'data' => [
                    'type' => 'log',
                    'level' => 'error',
                    'message' => 'Could not resolve host.',
                ],
                'status' => ResultStatus::Failed,
            ]);

            SpeedtestFailed::dispatch($this->result);

            return false;
        }

        return true;
    }
}
