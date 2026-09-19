<?php

use App\Enums\BenchmarkMetric;
use App\Events\SpeedtestBenchmarkHealthy;
use App\Events\SpeedtestBenchmarkUnhealthy;
use App\Jobs\Ookla\BenchmarkSpeedtestJob;
use App\Models\Benchmark;
use App\Models\Result;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    // The migration seeds one fixed (disabled) row per metric; start each test clean.
    Benchmark::query()->delete();
});

it('does nothing when no benchmarks are enabled', function () {
    $result = Result::factory()->create(['scheduled' => true]);

    (new BenchmarkSpeedtestJob($result))->handle();

    expect($result->refresh()->benchmarks)->toBeNull();
});

it('marks the result healthy when every enabled benchmark passes', function () {
    Event::fake([SpeedtestBenchmarkHealthy::class, SpeedtestBenchmarkUnhealthy::class]);

    Benchmark::factory()->create([
        'metric' => BenchmarkMetric::Download,
        'absolute_value' => 50,
    ]);

    $result = Result::factory()->create([
        'scheduled' => true,
        'download' => 12_500_000, // 100 Mbit
    ]);

    (new BenchmarkSpeedtestJob($result))->handle();

    $result->refresh();

    expect($result->healthy)->toBeTrue()
        ->and($result->benchmarks['download']['passed'])->toBeTrue();

    Event::assertDispatched(SpeedtestBenchmarkHealthy::class);
    Event::assertNotDispatched(SpeedtestBenchmarkUnhealthy::class);
});

it('marks the result unhealthy and includes packet loss when a benchmark fails', function () {
    Event::fake([SpeedtestBenchmarkHealthy::class, SpeedtestBenchmarkUnhealthy::class]);

    Benchmark::factory()->create([
        'metric' => BenchmarkMetric::PacketLoss,
        'absolute_value' => 5,
    ]);

    $result = Result::factory()->create([
        'scheduled' => true,
        'data' => ['packetLoss' => 20],
    ]);

    (new BenchmarkSpeedtestJob($result))->handle();

    $result->refresh();

    expect($result->healthy)->toBeFalse()
        ->and($result->benchmarks['packet_loss']['passed'])->toBeFalse()
        ->and($result->benchmarks['packet_loss']['unit'])->toBe('%');

    Event::assertDispatched(SpeedtestBenchmarkUnhealthy::class);
});
