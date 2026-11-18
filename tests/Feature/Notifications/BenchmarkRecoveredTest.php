<?php

use App\Enums\BenchmarkMetric;
use App\Events\SpeedtestBenchmarkHealthy;
use App\Events\SpeedtestBenchmarkRecovered;
use App\Events\SpeedtestBenchmarkUnhealthy;
use App\Jobs\Ookla\BenchmarkSpeedtestJob;
use App\Models\Benchmark;
use App\Models\Result;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake([SpeedtestBenchmarkHealthy::class, SpeedtestBenchmarkUnhealthy::class, SpeedtestBenchmarkRecovered::class]);

    // The migration seeds one fixed (disabled) row per metric; start each test clean.
    Benchmark::query()->delete();

    Benchmark::factory()->create([
        'metric' => BenchmarkMetric::Download,
        'absolute_value' => 50,
    ]);
});

function benchmarkHealthyResult(): Result
{
    $result = Result::factory()->create([
        'scheduled' => true,
        'download' => 12_500_000, // 100 Mbit
    ]);

    (new BenchmarkSpeedtestJob($result))->handle();

    return $result;
}

it('fires a recovered event when a healthy result follows an unhealthy one', function () {
    Result::factory()->create(['healthy' => false]);

    benchmarkHealthyResult();

    Event::assertDispatched(SpeedtestBenchmarkHealthy::class);
    Event::assertDispatched(SpeedtestBenchmarkRecovered::class);
});

it('ignores results that were not benchmarked when looking for the previous one', function () {
    Result::factory()->create(['healthy' => false]);
    Result::factory()->create(['healthy' => null]);

    benchmarkHealthyResult();

    Event::assertDispatched(SpeedtestBenchmarkRecovered::class);
});

it('does not fire a recovered event when the previous result was healthy', function () {
    Result::factory()->create(['healthy' => true]);

    benchmarkHealthyResult();

    Event::assertNotDispatched(SpeedtestBenchmarkRecovered::class);
});

it('does not fire a recovered event for the first benchmarked result', function () {
    benchmarkHealthyResult();

    Event::assertNotDispatched(SpeedtestBenchmarkRecovered::class);
});
