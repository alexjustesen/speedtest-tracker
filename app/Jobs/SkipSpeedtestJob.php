<?php

namespace App\Jobs;

use App\Actions\GetExternalIpAddress;
use App\Enums\ResultStatus;
use App\Events\SpeedtestSkipped;
use App\Helpers\Network;
use App\Models\Result;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SkipSpeedtestJob implements ShouldQueue
{
    use Batchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Result $result,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        /**
         * Only skip IPs for scheduled tests.
         */
        if ($this->result->scheduled === false) {
            return;
        }

        $externalIp = GetExternalIpAddress::run();

        $shouldSkip = $this->shouldSkip(
            externalIp: $externalIp,
        );

        if ($shouldSkip === false) {
            return;
        }

        $this->result->update([
            'data->type' => 'log',
            'data->level' => 'error',
            'data->message' => $shouldSkip,
            'interface->externalIp' => $externalIp,
            'status' => ResultStatus::Skipped,
        ]);

        SpeedtestSkipped::dispatch($this->result);

        $this->batch()->cancel();
    }

    /**
     * Check if the test should be skipped.
     */
    private function shouldSkip(string $externalIp): bool|string
    {
        $skipIPs = array_filter(
            array_map(
                'trim',
                explode(',', config('speedtest.skip_ips')),
            ),
        );

        if (count($skipIPs) < 1) {
            return false;
        }

        foreach ($skipIPs as $ip) {
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
