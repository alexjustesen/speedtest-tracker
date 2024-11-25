<?php

namespace App\Jobs;

use App\Helpers\Benchmark;
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
        // If the batch is cancelled, do nothing
        if ($this->batch()->cancelled()) {
            return;
        }

        // Retrieve the benchmarks from the result
        $benchmarks = $this->result->benchmarks;

        // Process the benchmarks (assuming you have download, upload, ping types)
        $types = ['download', 'upload', 'ping'];

        foreach ($types as $type) {
            $value = $this->result->{$type};

            // Retrieve the benchmark settings for the given type
            $benchmark = Arr::get($benchmarks, $type);

            // Only check the benchmark if the value and benchmark are valid
            if ($benchmark && $value !== null) {
                if ($type === 'ping') {
                    // Use the ping method for the ping benchmark
                    $passed = Benchmark::ping($value, $benchmark);
                } else {
                    // Use the bitrate method for download/upload benchmarks
                    $passed = Benchmark::bitrate($value, $benchmark);
                }

                // Invert the result logic here:
                $passedStatus = ! $passed ? 'true' : 'false';

                // If the result has changed, update the passed status
                if (Arr::get($benchmarks, "$type.passed") !== $passedStatus) {
                    $benchmarks[$type]['passed'] = $passedStatus;
                }
            }
        }

        // After processing, update the result with the modified benchmarks
        $this->result->benchmarks = $benchmarks;
        $this->result->save();
    }
}
