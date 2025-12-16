<?php

namespace App\Jobs\Ookla;

use App\Actions\GetExternalIpAddress;
use App\Enums\ResultStatus;
use App\Events\SpeedtestChecking;
use App\Events\SpeedtestFailed;
use App\Events\SpeedtestSkipped;
use App\Helpers\Network;
use App\Models\Result;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Spatie\Ping\Ping;

class PreflightSpeedtestJob implements ShouldQueue
{
    use Batchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Result $result,
    ) {}

    /**
     * Get the middleware the job should pass through.
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
            'status' => ResultStatus::Checking,
        ]);

        SpeedtestChecking::dispatch($this->result);

        // TODO: 1. Check for internet connectivity.
        if (! $this->checkInternetConnectivity()) {
            SpeedtestFailed::dispatch($this->result);

            $this->batch()->cancel();
        }

        // TODO: 2. Decide whether to skip based on IP address.
        if ($this->shouldSkip() === true) {
            SpeedtestSkipped::dispatch($this->result);

            $this->batch()->cancel();
        }
    }

    /**
     * Check internet connectivity.
     */
    private function checkInternetConnectivity(): bool
    {
        $hostname = config('speedtest.preflight.check_internet_connectivity');
        $hostname = preg_replace('#^https?://#', '', $hostname);

        $response = (new Ping(
            hostname: $hostname,
        ))->run();

        if ($response->hasError()) {
            $this->result->update([
                'data->type' => 'log',
                'data->level' => 'error',
                'data->message' => 'Failed to verify internet connectivity using hostname: "' . $hostname . '".',
                'status' => ResultStatus::Failed,
            ]);
        }

        return $response->isSuccess();
    }

    /**
     * Decide whether to skip the speedtest based on IP address.
     */
    private function shouldSkip(): bool
    {
        // Only skip IPs for scheduled tests.
        if ($this->result->scheduled === false) {
            return false;
        }

        $skipIPs = array_filter(
            array_map(
                'trim',
                explode(',', config('speedtest.preflight.skip_ips')),
            ),
        );

        if (! count($skipIPs) > 0) {
            return false;
        }

        $externalIp = GetExternalIpAddress::run();

        foreach ($skipIPs as $ip) {
            // Check for exact IP match
            if (filter_var($ip, FILTER_VALIDATE_IP) && $externalIp === $ip) {
                $this->result->update([
                    'data->type' => 'log',
                    'data->level' => 'error',
                    'data->message' => sprintf('"%s" was found in external IP address skip list.', $externalIp),
                    'data->interface->externalIp' => $externalIp,
                    'status' => ResultStatus::Skipped,
                ]);

                return true;
            }

            // Check for IP range match
            if (strpos($ip, '/') !== false && Network::ipInRange($externalIp, $ip)) {
                $this->result->update([
                    'data->type' => 'log',
                    'data->level' => 'error',
                    'data->message' => sprintf('"%s" was found in external IP address skip list within range "%s".', $externalIp, $ip),
                    'data->interface->externalIp' => $externalIp,
                    'status' => ResultStatus::Skipped,
                ]);

                return true;
            }
        }

        return false;
    }
}
