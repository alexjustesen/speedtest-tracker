<?php

use App\Livewire\MetricsDashboard;
use App\Models\Result;
use Livewire\Livewire;

it('returns individual results not aggregated by date', function () {
    // Create 5 results on the same day but different times
    Result::factory()->count(5)->create([
        'created_at' => now()->subHours(2),
        'download' => 125000000, // 125 MB/s = 1000 Mbps
        'upload' => 50000000, // 50 MB/s = 400 Mbps
    ]);

    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'today']);
    $chartData = $component->instance()->getChartData();

    // Should return 5 individual results, not 1 aggregated day
    expect($chartData['labels'])->toHaveCount(5);
    expect($chartData['download'])->toHaveCount(5);
    expect($chartData['upload'])->toHaveCount(5);
});

it('formats labels as time only for today range', function () {
    Result::factory()->create([
        'created_at' => now()->setTime(14, 30, 0),
        'download' => 125000000,
        'upload' => 50000000,
    ]);

    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'today']);
    $chartData = $component->instance()->getChartData();

    // Should match format like "2:30 PM"
    expect($chartData['labels'][0])->toMatch('/\d{1,2}:\d{2} [AP]M/');
});

it('formats labels as day and time for week range', function () {
    Result::factory()->create([
        'created_at' => now()->setTime(14, 30, 0),
        'download' => 125000000,
        'upload' => 50000000,
    ]);

    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'week']);
    $chartData = $component->instance()->getChartData();

    // Should match format like "Mon 2:30 PM"
    expect($chartData['labels'][0])->toMatch('/[A-Za-z]{3} \d{1,2}:\d{2} [AP]M/');
});

it('formats labels as date and time for month range', function () {
    Result::factory()->create([
        'created_at' => now()->setTime(14, 30, 0),
        'download' => 125000000,
        'upload' => 50000000,
    ]);

    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'month']);
    $chartData = $component->instance()->getChartData();

    // Should match format like "Dec 15 2:30 PM"
    expect($chartData['labels'][0])->toMatch('/[A-Za-z]{3} \d{1,2} \d{1,2}:\d{2} [AP]M/');
});

it('converts download speed from bytes per second to Mbps correctly', function () {
    // Create result with known download speed
    // 125,000,000 bytes/sec * 8 = 1,000,000,000 bits/sec = 1000 Mbps
    Result::factory()->create([
        'created_at' => now(),
        'download' => 125000000,
        'upload' => 50000000,
    ]);

    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'today']);
    $chartData = $component->instance()->getChartData();

    expect($chartData['download'][0])->toBe(1000.0);
});

it('converts upload speed from bytes per second to Mbps correctly', function () {
    // Create result with known upload speed
    // 50,000,000 bytes/sec * 8 = 400,000,000 bits/sec = 400 Mbps
    Result::factory()->create([
        'created_at' => now(),
        'download' => 125000000,
        'upload' => 50000000,
    ]);

    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'today']);
    $chartData = $component->instance()->getChartData();

    expect($chartData['upload'][0])->toBe(400.0);
});

it('handles null download values gracefully', function () {
    Result::factory()->create([
        'created_at' => now(),
        'download' => null,
        'upload' => 50000000,
    ]);

    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'today']);
    $chartData = $component->instance()->getChartData();

    expect($chartData['download'][0])->toBe(0.0);
});

it('handles null upload values gracefully', function () {
    Result::factory()->create([
        'created_at' => now(),
        'download' => 125000000,
        'upload' => null,
    ]);

    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'today']);
    $chartData = $component->instance()->getChartData();

    expect($chartData['upload'][0])->toBe(0.0);
});

it('returns empty arrays when no results exist', function () {
    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'today']);
    $chartData = $component->instance()->getChartData();

    expect($chartData['labels'])->toBeEmpty();
    expect($chartData['download'])->toBeEmpty();
    expect($chartData['upload'])->toBeEmpty();
    expect($chartData['count'])->toBe(0);
});

it('only includes completed results within date range', function () {
    // Create completed result within range
    Result::factory()->create([
        'created_at' => now()->subHour(),
    ]);

    // Create result outside range (should be excluded)
    Result::factory()->create([
        'created_at' => now()->subDays(2),
    ]);

    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'today']);
    $chartData = $component->instance()->getChartData();

    expect($chartData['count'])->toBe(1);
});

it('returns correct count of results', function () {
    Result::factory()->count(10)->create([
        'created_at' => now()->subHours(2),
    ]);

    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'today']);
    $chartData = $component->instance()->getChartData();

    expect($chartData['count'])->toBe(10);
});

it('orders results by created_at chronologically', function () {
    // Create results in reverse order
    $result1 = Result::factory()->create([
        'created_at' => now()->subHours(3),
        'download' => 100000000,
    ]);

    $result2 = Result::factory()->create([
        'created_at' => now()->subHours(2),
        'download' => 200000000,
    ]);

    $result3 = Result::factory()->create([
        'created_at' => now()->subHours(1),
        'download' => 300000000,
    ]);

    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'today']);
    $chartData = $component->instance()->getChartData();

    // Data should be ordered chronologically (oldest to newest)
    expect($chartData['download'])->toBe([800.0, 1600.0, 2400.0]);
});

it('calculates healthy ratio based on download benchmark KPI passed status', function () {
    // Create 4 results: 3 passed, 1 failed download benchmark
    Result::factory()->create([
        'created_at' => now()->subHours(4),
        'benchmarks' => [
            'download' => ['bar' => 'min', 'passed' => true, 'type' => 'absolute', 'value' => 100, 'unit' => 'mbps'],
        ],
    ]);

    Result::factory()->create([
        'created_at' => now()->subHours(3),
        'benchmarks' => [
            'download' => ['bar' => 'min', 'passed' => true, 'type' => 'absolute', 'value' => 100, 'unit' => 'mbps'],
        ],
    ]);

    Result::factory()->create([
        'created_at' => now()->subHours(2),
        'benchmarks' => [
            'download' => ['bar' => 'min', 'passed' => false, 'type' => 'absolute', 'value' => 100, 'unit' => 'mbps'],
        ],
    ]);

    Result::factory()->create([
        'created_at' => now()->subHours(1),
        'benchmarks' => [
            'download' => ['bar' => 'min', 'passed' => true, 'type' => 'absolute', 'value' => 100, 'unit' => 'mbps'],
        ],
    ]);

    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'today']);
    $chartData = $component->instance()->getChartData();

    // 3 out of 4 passed = 75%
    expect($chartData['downloadStats']['healthy'])->toBe(75.0);
});

it('calculates healthy ratio based on upload benchmark KPI passed status', function () {
    // Create 5 results: 2 passed, 3 failed upload benchmark
    Result::factory()->create([
        'created_at' => now()->subHours(5),
        'benchmarks' => [
            'upload' => ['bar' => 'min', 'passed' => true, 'type' => 'absolute', 'value' => 50, 'unit' => 'mbps'],
        ],
    ]);

    Result::factory()->create([
        'created_at' => now()->subHours(4),
        'benchmarks' => [
            'upload' => ['bar' => 'min', 'passed' => false, 'type' => 'absolute', 'value' => 50, 'unit' => 'mbps'],
        ],
    ]);

    Result::factory()->create([
        'created_at' => now()->subHours(3),
        'benchmarks' => [
            'upload' => ['bar' => 'min', 'passed' => false, 'type' => 'absolute', 'value' => 50, 'unit' => 'mbps'],
        ],
    ]);

    Result::factory()->create([
        'created_at' => now()->subHours(2),
        'benchmarks' => [
            'upload' => ['bar' => 'min', 'passed' => false, 'type' => 'absolute', 'value' => 50, 'unit' => 'mbps'],
        ],
    ]);

    Result::factory()->create([
        'created_at' => now()->subHours(1),
        'benchmarks' => [
            'upload' => ['bar' => 'min', 'passed' => true, 'type' => 'absolute', 'value' => 50, 'unit' => 'mbps'],
        ],
    ]);

    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'today']);
    $chartData = $component->instance()->getChartData();

    // 2 out of 5 passed = 40%
    expect($chartData['uploadStats']['healthy'])->toBe(40.0);
});

it('calculates healthy ratio based on ping benchmark KPI passed status', function () {
    // Create 10 results: all passed ping benchmark
    Result::factory()->count(10)->create([
        'created_at' => now()->subHours(1),
        'benchmarks' => [
            'ping' => ['bar' => 'max', 'passed' => true, 'type' => 'absolute', 'value' => 100, 'unit' => 'ms'],
        ],
    ]);

    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'today']);
    $chartData = $component->instance()->getChartData();

    // 10 out of 10 passed = 100%
    expect($chartData['pingStats']['healthy'])->toBe(100.0);
});

it('calculates separate healthy ratios for each metric', function () {
    // Create results with different benchmark results per metric
    Result::factory()->create([
        'created_at' => now()->subHours(3),
        'benchmarks' => [
            'download' => ['bar' => 'min', 'passed' => true, 'type' => 'absolute', 'value' => 100, 'unit' => 'mbps'],
            'upload' => ['bar' => 'min', 'passed' => false, 'type' => 'absolute', 'value' => 50, 'unit' => 'mbps'],
            'ping' => ['bar' => 'max', 'passed' => true, 'type' => 'absolute', 'value' => 100, 'unit' => 'ms'],
        ],
    ]);

    Result::factory()->create([
        'created_at' => now()->subHours(2),
        'benchmarks' => [
            'download' => ['bar' => 'min', 'passed' => false, 'type' => 'absolute', 'value' => 100, 'unit' => 'mbps'],
            'upload' => ['bar' => 'min', 'passed' => false, 'type' => 'absolute', 'value' => 50, 'unit' => 'mbps'],
            'ping' => ['bar' => 'max', 'passed' => true, 'type' => 'absolute', 'value' => 100, 'unit' => 'ms'],
        ],
    ]);

    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'today']);
    $chartData = $component->instance()->getChartData();

    // Download: 1 passed out of 2 = 50%
    expect($chartData['downloadStats']['healthy'])->toBe(50.0);
    // Upload: 0 passed out of 2 = 0%
    expect($chartData['uploadStats']['healthy'])->toBe(0.0);
    // Ping: 2 passed out of 2 = 100%
    expect($chartData['pingStats']['healthy'])->toBe(100.0);
});

it('handles results without benchmarks in healthy ratio calculation', function () {
    // Create results without benchmark data
    Result::factory()->count(3)->create([
        'created_at' => now()->subHours(2),
        'benchmarks' => null,
    ]);

    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'today']);
    $chartData = $component->instance()->getChartData();

    // No benchmarks means no passed tests = 0%
    expect($chartData['downloadStats']['healthy'])->toBe(0.0);
    expect($chartData['uploadStats']['healthy'])->toBe(0.0);
    expect($chartData['pingStats']['healthy'])->toBe(0.0);
});

it('returns 0 healthy ratio when no results exist', function () {
    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'today']);
    $chartData = $component->instance()->getChartData();

    expect($chartData['downloadStats']['healthy'])->toBe(0.0);
    expect($chartData['uploadStats']['healthy'])->toBe(0.0);
    expect($chartData['pingStats']['healthy'])->toBe(0.0);
});

it('marks latest stat as failed when the most recent benchmark failed', function () {
    // Create older result that passed
    Result::factory()->create([
        'created_at' => now()->subHours(2),
        'benchmarks' => [
            'download' => ['bar' => 'min', 'passed' => true, 'type' => 'absolute', 'value' => 100, 'unit' => 'mbps'],
            'upload' => ['bar' => 'min', 'passed' => true, 'type' => 'absolute', 'value' => 50, 'unit' => 'mbps'],
            'ping' => ['bar' => 'max', 'passed' => true, 'type' => 'absolute', 'value' => 100, 'unit' => 'ms'],
        ],
    ]);

    // Create most recent result that failed benchmarks
    Result::factory()->create([
        'created_at' => now()->subHours(1),
        'benchmarks' => [
            'download' => ['bar' => 'min', 'passed' => false, 'type' => 'absolute', 'value' => 100, 'unit' => 'mbps'],
            'upload' => ['bar' => 'min', 'passed' => false, 'type' => 'absolute', 'value' => 50, 'unit' => 'mbps'],
            'ping' => ['bar' => 'max', 'passed' => false, 'type' => 'absolute', 'value' => 100, 'unit' => 'ms'],
        ],
    ]);

    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'today']);
    $chartData = $component->instance()->getChartData();

    expect($chartData['downloadStats']['latestFailed'])->toBeTrue();
    expect($chartData['uploadStats']['latestFailed'])->toBeTrue();
    expect($chartData['pingStats']['latestFailed'])->toBeTrue();
});

it('marks latest stat as not failed when the most recent benchmark passed', function () {
    // Create older result that failed
    Result::factory()->create([
        'created_at' => now()->subHours(2),
        'benchmarks' => [
            'download' => ['bar' => 'min', 'passed' => false, 'type' => 'absolute', 'value' => 100, 'unit' => 'mbps'],
            'upload' => ['bar' => 'min', 'passed' => false, 'type' => 'absolute', 'value' => 50, 'unit' => 'mbps'],
            'ping' => ['bar' => 'max', 'passed' => false, 'type' => 'absolute', 'value' => 100, 'unit' => 'ms'],
        ],
    ]);

    // Create most recent result that passed benchmarks
    Result::factory()->create([
        'created_at' => now()->subHours(1),
        'benchmarks' => [
            'download' => ['bar' => 'min', 'passed' => true, 'type' => 'absolute', 'value' => 100, 'unit' => 'mbps'],
            'upload' => ['bar' => 'min', 'passed' => true, 'type' => 'absolute', 'value' => 50, 'unit' => 'mbps'],
            'ping' => ['bar' => 'max', 'passed' => true, 'type' => 'absolute', 'value' => 100, 'unit' => 'ms'],
        ],
    ]);

    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'today']);
    $chartData = $component->instance()->getChartData();

    expect($chartData['downloadStats']['latestFailed'])->toBeFalse();
    expect($chartData['uploadStats']['latestFailed'])->toBeFalse();
    expect($chartData['pingStats']['latestFailed'])->toBeFalse();
});

it('returns false for latestFailed when no results exist', function () {
    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'today']);
    $chartData = $component->instance()->getChartData();

    expect($chartData['downloadStats']['latestFailed'])->toBeFalse();
    expect($chartData['uploadStats']['latestFailed'])->toBeFalse();
    expect($chartData['pingStats']['latestFailed'])->toBeFalse();
});
