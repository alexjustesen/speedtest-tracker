<?php

namespace App\Jobs\Ookla;

use App\Enums\ResultStatus;
use App\Events\SpeedtestBenchmarkFailed;
use App\Events\SpeedtestBenchmarking;
use App\Events\SpeedtestBenchmarkPassed;
use App\Helpers\Benchmark;
use App\Models\Result;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class BenchmarkSpeedtestJob implements ShouldQueue
{
    use Batchable, Queueable;

    public bool $healthy = true;

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
        $schedule = $this->result->schedule;
        $thresholds = $schedule?->thresholds ?? [];

        if (! ($thresholds['enabled'] ?? false)) {
            return;
        }

        $this->result->update([
            'status' => ResultStatus::Benchmarking,
        ]);

        SpeedtestBenchmarking::dispatch($this->result);

        $benchmarks = $this->benchmark(
            result: $this->result,
            thresholds: $thresholds,
        );

        if (! count($benchmarks)) {
            return;
        }

        $this->result->update([
            'benchmarks' => $benchmarks,
            'healthy' => $this->healthy,
        ]);

        $this->healthy
            ? SpeedtestBenchmarkPassed::dispatch($this->result)
            : SpeedtestBenchmarkFailed::dispatch($this->result);
    }

    private function benchmark(Result $result, array $thresholds): array
    {
        $benchmarks = [];

        if (! blank($thresholds['download']) && $thresholds['download'] > 0) {
            $benchmarks['download'] = [
                'bar' => 'min',
                'passed' => Benchmark::bitrate($result->download, ['value' => $thresholds['download'], 'unit' => 'mbps']),
                'type' => 'absolute',
                'value' => $thresholds['download'],
                'unit' => 'mbps',
            ];

            if (! $benchmarks['download']['passed']) {
                $this->healthy = false;
            }
        }

        if (! blank($thresholds['upload']) && $thresholds['upload'] > 0) {
            $benchmarks['upload'] = [
                'bar' => 'min',
                'passed' => Benchmark::bitrate($result->upload, ['value' => $thresholds['upload'], 'unit' => 'mbps']),
                'type' => 'absolute',
                'value' => $thresholds['upload'],
                'unit' => 'mbps',
            ];

            if (! $benchmarks['upload']['passed']) {
                $this->healthy = false;
            }
        }

        if (! blank($thresholds['ping']) && $thresholds['ping'] > 0) {
            $benchmarks['ping'] = [
                'bar' => 'max',
                'passed' => Benchmark::ping($result->ping, ['value' => $thresholds['ping']]),
                'type' => 'absolute',
                'value' => $thresholds['ping'],
                'unit' => 'ms',
            ];

            if (! $benchmarks['ping']['passed']) {
                $this->healthy = false;
            }
        }

        return $benchmarks;
    }
}
