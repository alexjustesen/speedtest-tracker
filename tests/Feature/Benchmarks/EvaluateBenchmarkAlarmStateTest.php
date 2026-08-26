<?php

use App\Actions\Benchmarks\EvaluateBenchmarkAlarmState;
use App\Enums\BenchmarkMetric;
use App\Enums\BenchmarkState;
use App\Models\Benchmark;
use App\Models\Result;

beforeEach(function () {
    // The migration seeds one fixed row per metric; start each test clean.
    Benchmark::query()->delete();
});

/**
 * Create a scheduled result with a recorded (passing or failing) download benchmark entry.
 */
function scheduledBenchmarkResult(bool $passed): Result
{
    return Result::factory()->create([
        'scheduled' => true,
        'download' => $passed ? 12_500_000 : 1_250_000, // 100 Mbit or 10 Mbit
        'benchmarks' => [
            'download' => ['bar' => 'min', 'passed' => $passed, 'type' => 'absolute', 'test_value' => $passed ? 100 : 10, 'benchmark_value' => 50, 'unit' => 'mbps'],
        ],
    ]);
}

it('does not report a state until enough history exists', function () {
    $benchmark = Benchmark::factory()->create([
        'metric' => BenchmarkMetric::Download,
        'absolute_value' => 50,
        'consecutive_breaches' => 3,
    ]);

    $result = scheduledBenchmarkResult(passed: false);

    expect(EvaluateBenchmarkAlarmState::run($benchmark, $result))->toBeNull()
        ->and($benchmark->refresh()->state)->toBe(BenchmarkState::Ok);
});

it('triggers an alarm exactly once the consecutive breach threshold is met', function () {
    $benchmark = Benchmark::factory()->create([
        'metric' => BenchmarkMetric::Download,
        'absolute_value' => 50,
        'consecutive_breaches' => 3,
    ]);

    scheduledBenchmarkResult(passed: false);
    scheduledBenchmarkResult(passed: false);
    $third = scheduledBenchmarkResult(passed: false);

    expect(EvaluateBenchmarkAlarmState::run($benchmark, $third))->toBe(BenchmarkState::Alarm)
        ->and($benchmark->refresh()->state)->toBe(BenchmarkState::Alarm)
        ->and($benchmark->state_changed_at)->not->toBeNull();
});

it('does not re-fire on subsequent breaches once already in alarm by default', function () {
    $benchmark = Benchmark::factory()->create([
        'metric' => BenchmarkMetric::Download,
        'absolute_value' => 50,
        'consecutive_breaches' => 1,
        'repeat_while_in_alarm' => false,
    ]);

    $first = scheduledBenchmarkResult(passed: false);
    expect(EvaluateBenchmarkAlarmState::run($benchmark, $first))->toBe(BenchmarkState::Alarm);

    $second = scheduledBenchmarkResult(passed: false);
    expect(EvaluateBenchmarkAlarmState::run($benchmark, $second))->toBeNull();
});

it('repeats the alarm on every breach when repeat_while_in_alarm is enabled', function () {
    $benchmark = Benchmark::factory()->create([
        'metric' => BenchmarkMetric::Download,
        'absolute_value' => 50,
        'consecutive_breaches' => 1,
        'repeat_while_in_alarm' => true,
    ]);

    $first = scheduledBenchmarkResult(passed: false);
    expect(EvaluateBenchmarkAlarmState::run($benchmark, $first))->toBe(BenchmarkState::Alarm);

    $second = scheduledBenchmarkResult(passed: false);
    expect(EvaluateBenchmarkAlarmState::run($benchmark, $second))->toBe(BenchmarkState::Alarm);
});

it('fires a recovery exactly once when a test passes again', function () {
    $benchmark = Benchmark::factory()->inAlarm()->create([
        'metric' => BenchmarkMetric::Download,
        'absolute_value' => 50,
        'consecutive_breaches' => 1,
    ]);

    $passing = scheduledBenchmarkResult(passed: true);

    expect(EvaluateBenchmarkAlarmState::run($benchmark, $passing))->toBe(BenchmarkState::Ok)
        ->and($benchmark->refresh()->state)->toBe(BenchmarkState::Ok);

    $stillPassing = scheduledBenchmarkResult(passed: true);
    expect(EvaluateBenchmarkAlarmState::run($benchmark, $stillPassing))->toBeNull();
});

it('ignores unscheduled results', function () {
    $benchmark = Benchmark::factory()->create([
        'metric' => BenchmarkMetric::Download,
        'absolute_value' => 50,
        'consecutive_breaches' => 1,
    ]);

    $result = Result::factory()->create([
        'scheduled' => false,
        'download' => 1_250_000,
        'benchmarks' => ['download' => ['bar' => 'min', 'passed' => false, 'type' => 'absolute', 'test_value' => 10, 'benchmark_value' => 50, 'unit' => 'mbps']],
    ]);

    expect(EvaluateBenchmarkAlarmState::run($benchmark, $result))->toBeNull();
});
