<?php

namespace App\Jobs\Ookla;

use App\Actions\GetExternalIpAddress;
use App\Enums\ResultStatus;
use App\Events\SpeedtestChecking;
use App\Events\SpeedtestFailed;
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
        if ($this->shouldSkip()) {

        }

        // TODO: 3. Select server if server ID not provided.
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

        $externalIp = GetExternalIpAddress::run();

        return false;
    }

    /**
     * Select speedtest server if server ID not provided.
     */
    private function selectServer(?int $serverId): ?int
    {
        // Implement server selection logic here.
        return $serverId;
    }
}
