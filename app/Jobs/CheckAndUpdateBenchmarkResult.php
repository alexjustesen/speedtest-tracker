<?php

namespace App\Jobs;

use App\Models\Result;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Arr;

class CheckAndUpdateBenchmarkResult implements ShouldQueue
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

        // Retrieve benchmarks from the result
        $benchmarks = $this->result->benchmarks;

        // Convert bits to Mbps for download and upload
        $downloadInMbps = $this->result->download_bits !== null
            ? $this->convertBitsToMbps($this->result->download_bits)
            : null;

        $uploadInMbps = $this->result->upload_bits !== null
            ? $this->convertBitsToMbps($this->result->upload_bits)
            : null;

        // Check and add results to benchmarks only if the benchmarks exist
        $downloadBenchmark = Arr::get($benchmarks, 'download');
        $uploadBenchmark = Arr::get($benchmarks, 'upload');
        $pingBenchmark = Arr::get($benchmarks, 'ping');

        // Proceed only if the benchmark exists
        if ($downloadBenchmark) {
            $benchmarks = $this->addBenchmarkResult($benchmarks, 'download', $downloadInMbps);
        }

        if ($uploadBenchmark) {
            $benchmarks = $this->addBenchmarkResult($benchmarks, 'upload', $uploadInMbps);
        }

        if ($pingBenchmark) {
            $benchmarks = $this->addBenchmarkResult($benchmarks, 'ping', $this->result->ping);
        }

        // Only update the result if there were any changes to the benchmarks
        if ($this->result->benchmarks !== $benchmarks) {
            $this->result->update([
                'benchmarks' => $benchmarks,
            ]);
        }
    }

    /**
     * Add the result of a benchmark check to the benchmarks array.
     */
    private function addBenchmarkResult(array $benchmarks, string $type, $value): array
    {
        $benchmark = Arr::get($benchmarks, $type); // Use Arr::get() to retrieve the benchmark

        $passed = $this->checkBenchmark($value, $benchmark) ? 'false' : 'true';
        $benchmarks[$type]['passed'] = $passed;

        return $benchmarks;
    }

    /**
     * Check if a metric breaches its benchmark.
     */
    private function checkBenchmark(?float $value, ?array $benchmark): bool
    {
        if ($value === null || $benchmark === null) {
            return false; // No value or benchmark to compare
        }

        $bar = $benchmark['bar'] ?? null;
        $threshold = $benchmark['value'] ?? null;

        if ($bar === 'min') {
            return $value < $threshold;
        } elseif ($bar === 'max') {
            return $value > $threshold;
        }

        return false; // Unknown benchmark type
    }

    /**
     * Convert bits to Mbps.
     */
    private function convertBitsToMbps(int $bits): float
    {
        return $bits / (1000 * 1000); // Convert to Mbps
    }
}
