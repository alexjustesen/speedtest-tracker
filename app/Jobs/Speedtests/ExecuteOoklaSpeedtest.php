<?php

namespace App\Jobs\Speedtests;

use App\Enums\ResultStatus;
use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestFailed;
use App\Events\SpeedtestSkipped;
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

        // Fetch public IP data
        $publicIpData = $this->getPublicIp();

        $currentIp = $publicIpData['query'] ?? 'unknown';
        $currentIsp = $publicIpData['isp'] ?? 'unknown';

        // Retrieve SPEEDTEST_SKIP_IP Settings
        $skipSettings = array_filter(array_map('trim', explode(';', config('speedtest.skip_ip'))));

        // Check Each Skip Setting
        foreach ($skipSettings as $setting) {

            if (filter_var($setting, FILTER_VALIDATE_IP)) {

                if ($currentIp === $setting) {
                    $this->markAsSkipped(
                        "Current public IP address ($currentIp) was listed to be skipped for testing.",
                        $currentIp,
                        $currentIsp
                    );

                    return;
                }
            } elseif (strpos($setting, '/') !== false) {

                if ($this->ipInSubnet($currentIp, $setting)) {
                    $this->markAsSkipped(
                        "Current public IP address ($currentIp) falls within the excluded subnet ($setting).",
                        $currentIp,
                        $currentIsp
                    );

                    return;
                }
            } elseif (strcasecmp($currentIsp, $setting) === 0) {
                $this->markAsSkipped(
                    "Current ISP ($currentIsp) was listed to be skipped for testing.",
                    $currentIp,
                    $currentIsp
                );

                return;
            }
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

            // Add server ID to the error message if it exists
            if ($this->serverId !== null) {
                $this->result->update([
                    'data' => [
                        'type' => 'log',
                        'level' => 'error',
                        'message' => $errorMessage,
                        'server' => [
                            'id' => $this->serverId,
                        ],
                    ],
                    'status' => ResultStatus::Failed,
                ]);
            } else {
                $this->result->update([
                    'data' => [
                        'type' => 'log',
                        'level' => 'error',
                        'message' => $errorMessage,
                    ],
                    'status' => ResultStatus::Failed,
                ]);
            }

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

    protected function getPublicIp(): array
    {
        // Implement method to fetch public IP data
        $response = file_get_contents('http://ip-api.com/json/?fields=25088');

        return json_decode($response, true) ?? [];
    }

    protected function markAsSkipped(string $message, string $currentIp, string $currentIsp): void
    {
        $this->result->update([
            'data' => ['type' => 'log', 'level' => 'info', 'message' => $message],
            'data' => [
                'type' => 'log',
                'level' => 'info',
                'message' => $message,
                'isp' => $currentIsp,
                'interface' => [
                    'externalIp' => $currentIp,
                ],
            ],
            'status' => ResultStatus::Skipped, // Use Skipped status
        ]);
        SpeedtestSkipped::dispatch($this->result); // Dispatch skipped event
    }

    protected function markAsFailed(string $message): void
    {
        $this->result->update([
            'data' => ['type' => 'log', 'level' => 'error', 'message' => $message],
            'status' => ResultStatus::Failed,
        ]);
        SpeedtestFailed::dispatch($this->result);
    }

    protected function ipInSubnet(string $ip, string $subnet): bool
    {
        [$subnet, $mask] = explode('/', $subnet) + [1 => '32'];
        $subnetDecimal = ip2long($subnet);
        $ipDecimal = ip2long($ip);
        $maskDecimal = ~((1 << (32 - (int) $mask)) - 1);

        return ($subnetDecimal & $maskDecimal) === ($ipDecimal & $maskDecimal);
    }

    protected function checkForInternetConnection(): bool
    {
        $url = config('speedtest.ping_url');

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
