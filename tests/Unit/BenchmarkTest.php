<?php

use App\Enums\BenchmarkMetric;
use App\Enums\BenchmarkState;
use App\Enums\BenchmarkType;
use App\Models\Benchmark;
use App\Models\Result;

beforeEach(function () {
    // The migration seeds one fixed row per metric; start each test clean.
    Benchmark::query()->delete();
});

function makeBenchmarkResult(array $attributes = []): Result
{
    return Result::factory()->create(array_merge([
        'download' => 12_500_000, // 100 Mbit
        'upload' => 6_250_000, // 50 Mbit
        'ping' => 20,
        'data' => ['packetLoss' => 0],
        'scheduled' => true,
    ], $attributes));
}

it('casts attributes correctly', function () {
    $benchmark = Benchmark::factory()->create([
        'metric' => BenchmarkMetric::Download,
        'absolute_value' => 50,
    ]);

    expect($benchmark->refresh()->metric)->toBe(BenchmarkMetric::Download)
        ->and($benchmark->state)->toBe(BenchmarkState::Ok)
        ->and($benchmark->enabled)->toBeTrue()
        ->and($benchmark->absolute_value)->toBe(50.0);
});

it('clears the relative fields when switching to absolute', function () {
    $benchmark = Benchmark::factory()->relative(baseline: 100, percentage: 80)->create([
        'metric' => BenchmarkMetric::Download,
    ]);

    $benchmark->update([
        'type' => BenchmarkType::Absolute,
        'absolute_value' => 50,
    ]);

    expect($benchmark->refresh())
        ->baseline_value->toBeNull()
        ->relative_percentage->toBeNull()
        ->absolute_value->toBe(50.0);
});

it('clears the absolute value when switching to relative', function () {
    $benchmark = Benchmark::factory()->create([
        'metric' => BenchmarkMetric::Download,
        'absolute_value' => 50,
    ]);

    $benchmark->update([
        'type' => BenchmarkType::Relative,
        'baseline_value' => 100,
        'relative_percentage' => 80,
    ]);

    expect($benchmark->refresh())
        ->absolute_value->toBeNull()
        ->baseline_value->toBe(100.0)
        ->relative_percentage->toBe(80.0);
});

it('scopes to only enabled benchmarks', function () {
    Benchmark::factory()->create();
    Benchmark::factory()->create();
    Benchmark::factory()->disabled()->create();

    expect(Benchmark::enabled()->count())->toBe(2);
});

it('reports whether it is in alarm', function () {
    $ok = Benchmark::factory()->create();
    $alarm = Benchmark::factory()->inAlarm()->create();

    expect($ok->isInAlarm())->toBeFalse()
        ->and($alarm->isInAlarm())->toBeTrue();
});

it('passes an absolute download benchmark when the result meets it', function () {
    $benchmark = Benchmark::factory()->create([
        'metric' => BenchmarkMetric::Download,
        'absolute_value' => 90,
    ]);

    $result = makeBenchmarkResult();

    expect($benchmark->passes($result))->toBeTrue();
});

it('fails an absolute download benchmark when the result falls short', function () {
    $benchmark = Benchmark::factory()->create([
        'metric' => BenchmarkMetric::Download,
        'absolute_value' => 150,
    ]);

    $result = makeBenchmarkResult();

    expect($benchmark->passes($result))->toBeFalse();
});

it('passes an absolute ping benchmark when the result is below it', function () {
    $benchmark = Benchmark::factory()->create([
        'metric' => BenchmarkMetric::Ping,
        'absolute_value' => 30,
    ]);

    $result = makeBenchmarkResult(['ping' => 20]);

    expect($benchmark->passes($result))->toBeTrue();
});

it('fails an absolute ping benchmark when the result is at or above it', function () {
    $benchmark = Benchmark::factory()->create([
        'metric' => BenchmarkMetric::Ping,
        'absolute_value' => 30,
    ]);

    $result = makeBenchmarkResult(['ping' => 35]);

    expect($benchmark->passes($result))->toBeFalse();
});

it('computes a relative benchmark value from the ISP baseline and percentage', function () {
    $benchmark = Benchmark::factory()->relative(baseline: 100, percentage: 80)->create([
        'metric' => BenchmarkMetric::Download,
    ]);

    expect($benchmark->benchmarkValue())->toBe(80.0);
});

it('passes a relative download benchmark when the result meets the percentage of baseline', function () {
    $benchmark = Benchmark::factory()->relative(baseline: 100, percentage: 80)->create([
        'metric' => BenchmarkMetric::Download,
    ]);

    $result = makeBenchmarkResult(); // 100 Mbit, 80% of 100 = 80

    expect($benchmark->passes($result))->toBeTrue();
});

it('fails a relative download benchmark when the result falls short of the baseline percentage', function () {
    $benchmark = Benchmark::factory()->relative(baseline: 200, percentage: 80)->create([
        'metric' => BenchmarkMetric::Download,
    ]);

    $result = makeBenchmarkResult(); // 100 Mbit, 80% of 200 = 160

    expect($benchmark->passes($result))->toBeFalse();
});

it('passes packet loss benchmarks', function () {
    $benchmark = Benchmark::factory()->create([
        'metric' => BenchmarkMetric::PacketLoss,
        'absolute_value' => 5,
    ]);

    expect($benchmark->passes(makeBenchmarkResult(['data' => ['packetLoss' => 0]])))->toBeTrue()
        ->and($benchmark->passes(makeBenchmarkResult(['data' => ['packetLoss' => 10]])))->toBeFalse();
});

it('passes when there is no data to compare', function () {
    $benchmark = Benchmark::factory()->create([
        'metric' => BenchmarkMetric::Ping,
        'absolute_value' => 30,
    ]);

    $result = makeBenchmarkResult(['ping' => null]);

    expect($benchmark->passes($result))->toBeTrue();
});

it('passes when the relative benchmark is missing baseline data', function () {
    $benchmark = Benchmark::factory()->create([
        'metric' => BenchmarkMetric::Download,
        'type' => BenchmarkType::Relative,
        'absolute_value' => null,
        'baseline_value' => null,
        'relative_percentage' => null,
    ]);

    expect($benchmark->benchmarkValue())->toBeNull()
        ->and($benchmark->passes(makeBenchmarkResult()))->toBeTrue();
});
