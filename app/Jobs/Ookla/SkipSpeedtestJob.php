<?php

namespace App\Jobs\Ookla;

use App\Actions\GetExternalIpAddress;
use App\Enums\ResultStatus;
use App\Events\SpeedtestSkipped;
use App\Helpers\Network;
use App\Models\Result;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class SkipSpeedtestJob implements ShouldQueue
{
    use Batchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Result $result,
        public array $skipIps = [],
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
        /**
         * Only skip IPs for scheduled tests.
         */
        if ($this->result->scheduled === false) {
            return;
        }

        $externalIp = GetExternalIpAddress::run();

        $shouldSkip = $this->shouldSkip(
            externalIp: $externalIp,
            skipIps: $this->skipIps,
        );

        if ($shouldSkip === false) {
            return;
        }

        $this->result->update([
            'data->type' => 'log',
            'data->level' => 'error',
            'data->message' => $shouldSkip,
            'data->interface->externalIp' => $externalIp,
            'status' => ResultStatus::Skipped,
        ]);

        SpeedtestSkipped::dispatch($this->result);

        $this->batch()?->cancel();
    }

    /**
     * Check if the test should be skipped.
     */
    private function shouldSkip(string $externalIp, array $skipIps): bool|string
    {
        $skipIps = array_filter(array_map('trim', $skipIps));

        if (count($skipIps) < 1) {
            return false;
        }

        foreach ($skipIps as $ip) {
            // Check for exact IP match
            if (filter_var($ip, FILTER_VALIDATE_IP) && $externalIp === $ip) {
                return sprintf('"%s" was found in external IP address skip list.', $externalIp);
            }

            // Check for IP range match
            if (strpos($ip, '/') !== false && Network::ipInRange($externalIp, $ip)) {
                return sprintf('"%s" was found in external IP address skip list within range "%s".', $externalIp, $ip);
            }
        }

        return false;
    }
}
