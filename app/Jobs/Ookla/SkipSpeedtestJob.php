<?php

namespace App\Jobs\Ookla;

use App\Actions\GetExternalIpAddress;
use App\Enums\ResultStatus;
use App\Events\SpeedtestFailed;
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
        $skipIps = $this->getSkipIps();

        /**
         * Skip if test is not scheduled or no IPs are configured to skip.
         */
        if ($this->result->scheduled === false || empty($skipIps)) {
            return;
        }

        $externalIp = GetExternalIpAddress::run();

        if ($externalIp['ok'] === false) {
            $this->result->update([
                'data->type' => 'log',
                'data->level' => 'error',
                'data->message' => $externalIp['body'],
                'status' => ResultStatus::Failed,
            ]);

            SpeedtestFailed::dispatch($this->result);

            $this->batch()->cancel();

            return;
        }

        $shouldSkip = $this->shouldSkip(
            externalIp: $externalIp['body'],
            skipIps: $skipIps,
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

        $this->batch()->cancel();
    }

    /**
     * @return array<string>
     */
    private function getSkipIps(): array
    {
        return $this->result->speedtest?->skip_ips ?? [];
    }

    /**
     * Check if the test should be skipped.
     *
     * @param  array<string>  $skipIps
     */
    private function shouldSkip(string $externalIp, array $skipIps): bool|string
    {
        if (empty($skipIps)) {
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
