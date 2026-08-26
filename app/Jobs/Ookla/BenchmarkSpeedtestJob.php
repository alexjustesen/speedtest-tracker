<?php

namespace App\Jobs\Ookla;

use App\Actions\Benchmarks\EvaluateBenchmarkAlarmState;
use App\Enums\BenchmarkState;
use App\Enums\ResultStatus;
use App\Events\BenchmarkAlarmsRecovered;
use App\Events\BenchmarkAlarmsTriggered;
use App\Events\SpeedtestBenchmarkHealthy;
use App\Events\SpeedtestBenchmarking;
use App\Events\SpeedtestBenchmarkUnhealthy;
use App\Models\Benchmark;
use App\Models\Result;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
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
        $benchmarks = Benchmark::enabled()->get();

        if ($benchmarks->isEmpty()) {
            return;
        }

        $this->result->update([
            'status' => ResultStatus::Benchmarking,
        ]);

        SpeedtestBenchmarking::dispatch($this->result);

        $results = $this->benchmark($benchmarks);

        if (! count($results)) {
            return;
        }

        $this->result->update([
            'benchmarks' => $results,
            'healthy' => $this->healthy,
        ]);

        $this->healthy
            ? SpeedtestBenchmarkHealthy::dispatch($this->result)
            : SpeedtestBenchmarkUnhealthy::dispatch($this->result);

        $this->evaluateAlarmState($benchmarks);
    }

    /**
     * Evaluate each benchmark against the result and build the benchmarks array.
     *
     * @param  Collection<int, Benchmark>  $benchmarks
     * @return array<string, mixed>
     */
    private function benchmark(Collection $benchmarks): array
    {
        $results = [];

        foreach ($benchmarks as $benchmark) {
            $passed = $benchmark->passes($this->result);

            $results[$benchmark->metric->value] = [
                'bar' => $benchmark->metric->direction(),
                'passed' => $passed,
                'type' => $benchmark->type->value,
                'test_value' => $benchmark->currentValue($this->result),
                'benchmark_value' => $benchmark->benchmarkValue(),
                'unit' => $benchmark->metric->unit(),
            ];

            if (! $passed) {
                $this->healthy = false;
            }
        }

        return $results;
    }

    /**
     * Evaluate each benchmark's consecutive-breach alarm state and dispatch
     * batched notification events for any that transitioned or repeated.
     *
     * @param  Collection<int, Benchmark>  $benchmarks
     */
    private function evaluateAlarmState(Collection $benchmarks): void
    {
        $triggered = new Collection;
        $recovered = new Collection;

        foreach ($benchmarks as $benchmark) {
            $state = EvaluateBenchmarkAlarmState::run($benchmark, $this->result);

            match ($state) {
                BenchmarkState::Alarm => $triggered->push($benchmark),
                BenchmarkState::Ok => $recovered->push($benchmark),
                default => null,
            };
        }

        if ($triggered->isNotEmpty()) {
            BenchmarkAlarmsTriggered::dispatch($this->result, $triggered);
        }

        // Only notify on recovery once every enabled benchmark is passing
        // again, so a "recovered" notification never goes out while another
        // metric is still in alarm.
        $anyStillInAlarm = $benchmarks->contains(fn (Benchmark $benchmark): bool => $benchmark->state === BenchmarkState::Alarm);

        if ($recovered->isNotEmpty() && ! $anyStillInAlarm) {
            BenchmarkAlarmsRecovered::dispatch($this->result, $recovered);
        }
    }
}
