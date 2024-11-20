<?php

namespace App\Jobs\Speedtests;

use App\Actions\Helpers\GetExternalIpAddress;
use App\Enums\ResultStatus;
use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestFailed;
use App\Events\SpeedtestSkipped;
use App\Helpers\Network;
use App\Models\Result;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
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

        $externalIp = GetExternalIpAddress::run();

        $shouldSkip = $this->shouldSkip($externalIp);

        if ($shouldSkip !== false) {
            $this->markAsSkipped(
                message: $shouldSkip,
                externalIp: $externalIp,
            );

            return;
        }

        // Execute Speedtest
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
     * Mark the test as skipped with a specific message.
     */
    protected function markAsSkipped(string $message, string $externalIp): void
    {
        $this->result->update([
            'data' => [
                'type' => 'log',
                'level' => 'warning',
                'message' => $message,
                'interface' => [
                    'externalIp' => $externalIp,
                ],
            ],
            'status' => ResultStatus::Skipped,
        ]);

        SpeedtestSkipped::dispatch($this->result);
    }

    /**
     * Check for internet connection.
     *
     * @throws \Exception
     */
    protected function checkForInternetConnection(): bool
    {
        $url = config('speedtest.ping_url');

        // Skip checking for internet connection if ping url isn't set (disabled)

        if (blank($url)) {
            return true;
        }

        if (! $this->isValidPingUrl($url)) {
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

    /**
     * Check if the given URL is a valid ping URL.
     *
     * TODO: move to Network helper
     */
    public function isValidPingUrl(string $url): bool
    {
        $hasTLD = static function (string $url): bool {
            // this also ensures the string ends with a TLD
            return preg_match('/\.[a-z]{2,}$/i', $url);
        };

        return (filter_var($url, FILTER_VALIDATE_URL) && $hasTLD($url))
            // to check for things like `google.com`, we need to add the protocol
            || (filter_var('https://'.$url, FILTER_VALIDATE_URL) && $hasTLD($url))
            || filter_var($url, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 || FILTER_FLAG_IPV6) !== false;
    }

    /**
     * Check if the speedtest should be skipped based on the skip ips list.
     */
    public function shouldSkip(string $externalIp): bool|string
    {
        if (blank(config('speedtest.skip_ips'))) {
            return false;
        }

        $skipIPs = array_map('trim', explode(',', config('speedtest.skip_ips')));

        foreach ($skipIPs as $ip) {
            // Check for exact IP match
            if (filter_var($ip, FILTER_VALIDATE_IP) && $externalIp === $ip) {
                return sprintf('"%s" was found in public IP address skip list.', $externalIp);
            }

            // Check for IP range match
            if (strpos($ip, '/') !== false && Network::ipInRange($externalIp, $ip)) {
                return sprintf('"%s" was found in public IP address skip list within range "%s".', $externalIp, $ip);
            }
        }

        return false;
    }
}
