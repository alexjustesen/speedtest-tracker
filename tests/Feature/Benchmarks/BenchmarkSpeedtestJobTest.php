<?php

use App\Enums\BenchmarkMetric;
use App\Enums\BenchmarkState;
use App\Events\BenchmarkAlarmsRecovered;
use App\Events\BenchmarkAlarmsTriggered;
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
    Event::fake([SpeedtestBenchmarkHealthy::class, SpeedtestBenchmarkUnhealthy::class, BenchmarkAlarmsTriggered::class]);

    Benchmark::factory()->create([
        'metric' => BenchmarkMetric::PacketLoss,
        'absolute_value' => 5,
        'consecutive_breaches' => 1,
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
    Event::assertDispatched(BenchmarkAlarmsTriggered::class);
});

it('does not fire a recovery while another benchmark is still in alarm', function () {
    Event::fake([BenchmarkAlarmsRecovered::class]);

    $download = Benchmark::factory()->inAlarm()->create([
        'metric' => BenchmarkMetric::Download,
        'absolute_value' => 50,
        'consecutive_breaches' => 1,
    ]);

    $upload = Benchmark::factory()->inAlarm()->create([
        'metric' => BenchmarkMetric::Upload,
        'absolute_value' => 25,
        'consecutive_breaches' => 1,
    ]);

    $result = Result::factory()->create([
        'scheduled' => true,
        'download' => 1_250_000, // 10 Mbit, still fails
        'upload' => 6_250_000, // 50 Mbit, now passes
    ]);

    (new BenchmarkSpeedtestJob($result))->handle();

    Event::assertNotDispatched(BenchmarkAlarmsRecovered::class);
    expect($download->refresh()->state)->toBe(BenchmarkState::Alarm)
        ->and($upload->refresh()->state)->toBe(BenchmarkState::Ok);
});

it('fires a recovery once every enabled benchmark is passing again', function () {
    Event::fake([BenchmarkAlarmsRecovered::class]);

    Benchmark::factory()->create([
        'metric' => BenchmarkMetric::Download,
        'absolute_value' => 50,
        'consecutive_breaches' => 1,
        'state' => BenchmarkState::Ok,
    ]);

    $upload = Benchmark::factory()->inAlarm()->create([
        'metric' => BenchmarkMetric::Upload,
        'absolute_value' => 25,
        'consecutive_breaches' => 1,
    ]);

    $result = Result::factory()->create([
        'scheduled' => true,
        'download' => 12_500_000, // 100 Mbit, passes
        'upload' => 6_250_000, // 50 Mbit, now passes too
    ]);

    (new BenchmarkSpeedtestJob($result))->handle();

    Event::assertDispatched(BenchmarkAlarmsRecovered::class, fn (BenchmarkAlarmsRecovered $event): bool => $event->benchmarks->pluck('id')->contains($upload->id));
});
