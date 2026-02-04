<?php

namespace App\Jobs;

use App\Actions\PingHostname;
use App\Enums\ResultStatus;
use App\Events\SpeedtestChecking;
use App\Events\SpeedtestFailed;
use App\Models\Result;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class CheckForInternetConnectionJob implements ShouldQueue
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

        $ping = PingHostname::run();

        if ($ping?->isSuccess()) {
            return;
        }

        Log::debug('Pinged failed, falling back to HTTP connectivity check');

        // Ping either failed or was unavailable — attempt an HTTP fallback.
        if ($this->httpFallbackSucceeds()) {
            return;
        }

        $message = $ping === null
            ? 'Ping command is unavailable and HTTP fallback also failed.'
            : sprintf('Failed to connected to hostname "%s". Error received "%s". HTTP fallback also failed.', $ping->getHost(), $ping->error()?->value);

        $this->result->update([
            'data->type' => 'log',
            'data->level' => 'error',
            'data->message' => $message,
            'status' => ResultStatus::Failed,
        ]);

        SpeedtestFailed::dispatch($this->result);

        $this->batch()->cancel();
    }

    /**
     * Attempt to verify connectivity via an HTTP GET request as a fallback
     * when ping is unavailable or unsuccessful.
     */
    protected function httpFallbackSucceeds(): bool
    {
        $url = config('speedtest.preflight.external_ip_url');

        try {
            $response = Http::retry(3, 100)
                ->timeout(5)
                ->get(url: $url);

            if ($response->ok()) {
                Log::debug('HTTP fallback connectivity check succeeded', ['url' => $url]);

                return true;
            }

            Log::debug('HTTP fallback connectivity check received non-OK response', [
                'url' => $url,
                'status' => $response->status(),
            ]);

            return false;
        } catch (Throwable $e) {
            Log::debug('HTTP fallback connectivity check failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
