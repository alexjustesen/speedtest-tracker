<?php

use App\Enums\ResultStatus;
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

    $component = Livewire::test(MetricsDashboard::class);
    $chartData = $component->instance()->getChartData();

    // Should return 5 individual results, not 1 aggregated day
    expect($chartData['labels'])->toHaveCount(5);
    expect($chartData['download'])->toHaveCount(5);
    expect($chartData['upload'])->toHaveCount(5);
});

it('formats labels as time only for 1 day or less range', function () {
    Result::factory()->create([
        'created_at' => now()->setTime(14, 30, 0),
        'download' => 125000000,
        'upload' => 50000000,
    ]);

    $component = Livewire::test(MetricsDashboard::class);
    $component->set('startDate', now()->subDay()->format('Y-m-d'));
    $component->set('endDate', now()->format('Y-m-d'));
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

    $component = Livewire::test(MetricsDashboard::class);
    $component->set('startDate', now()->subWeek()->format('Y-m-d'));
    $component->set('endDate', now()->format('Y-m-d'));
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

    $component = Livewire::test(MetricsDashboard::class);
    $component->set('startDate', now()->subMonth()->format('Y-m-d'));
    $component->set('endDate', now()->format('Y-m-d'));
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

    $component = Livewire::test(MetricsDashboard::class);
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

    $component = Livewire::test(MetricsDashboard::class);
    $chartData = $component->instance()->getChartData();

    expect($chartData['upload'][0])->toBe(400.0);
});

it('handles null download values gracefully', function () {
    Result::factory()->create([
        'created_at' => now(),
        'download' => null,
        'upload' => 50000000,
    ]);

    $component = Livewire::test(MetricsDashboard::class);
    $chartData = $component->instance()->getChartData();

    expect($chartData['download'][0])->toBe(0.0);
});

it('handles null upload values gracefully', function () {
    Result::factory()->create([
        'created_at' => now(),
        'download' => 125000000,
        'upload' => null,
    ]);

    $component = Livewire::test(MetricsDashboard::class);
    $chartData = $component->instance()->getChartData();

    expect($chartData['upload'][0])->toBe(0.0);
});

it('returns empty arrays when no results exist', function () {
    $component = Livewire::test(MetricsDashboard::class);
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

    $component = Livewire::test(MetricsDashboard::class);
    $chartData = $component->instance()->getChartData();

    expect($chartData['count'])->toBe(1);
});

it('returns correct count of results', function () {
    Result::factory()->count(10)->create([
        'created_at' => now()->subHours(2),
    ]);

    $component = Livewire::test(MetricsDashboard::class);
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

    $component = Livewire::test(MetricsDashboard::class);
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

    $component = Livewire::test(MetricsDashboard::class);
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

    $component = Livewire::test(MetricsDashboard::class);
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

    $component = Livewire::test(MetricsDashboard::class);
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

    $component = Livewire::test(MetricsDashboard::class);
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

    $component = Livewire::test(MetricsDashboard::class);
    $chartData = $component->instance()->getChartData();

    // No benchmarks means no passed tests = 0%
    expect($chartData['downloadStats']['healthy'])->toBe(0.0);
    expect($chartData['uploadStats']['healthy'])->toBe(0.0);
    expect($chartData['pingStats']['healthy'])->toBe(0.0);
});

it('returns 0 healthy ratio when no results exist', function () {
    $component = Livewire::test(MetricsDashboard::class);
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

    $component = Livewire::test(MetricsDashboard::class);
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

    $component = Livewire::test(MetricsDashboard::class);
    $chartData = $component->instance()->getChartData();

    expect($chartData['downloadStats']['latestFailed'])->toBeFalse();
    expect($chartData['uploadStats']['latestFailed'])->toBeFalse();
    expect($chartData['pingStats']['latestFailed'])->toBeFalse();
});

it('filters only scheduled results when scheduled filter is set', function () {
    // Create scheduled and unscheduled results
    Result::factory()->count(3)->create([
        'created_at' => now()->subHours(2),
        'scheduled' => true,
    ]);

    Result::factory()->count(2)->create([
        'created_at' => now()->subHours(2),
        'scheduled' => false,
    ]);

    $component = Livewire::test(MetricsDashboard::class);
    $component->set('scheduledFilter', 'scheduled');
    $chartData = $component->instance()->getChartData();

    expect($chartData['count'])->toBe(3);
});

it('filters only unscheduled results when unscheduled filter is set', function () {
    // Create scheduled and unscheduled results
    Result::factory()->count(3)->create([
        'created_at' => now()->subHours(2),
        'scheduled' => true,
    ]);

    Result::factory()->count(4)->create([
        'created_at' => now()->subHours(2),
        'scheduled' => false,
    ]);

    $component = Livewire::test(MetricsDashboard::class);
    $component->set('scheduledFilter', 'unscheduled');
    $chartData = $component->instance()->getChartData();

    expect($chartData['count'])->toBe(4);
});

it('returns all results when scheduled filter is set to all', function () {
    // Create scheduled and unscheduled results
    Result::factory()->count(3)->create([
        'created_at' => now()->subHours(2),
        'scheduled' => true,
    ]);

    Result::factory()->count(2)->create([
        'created_at' => now()->subHours(2),
        'scheduled' => false,
    ]);

    $component = Livewire::test(MetricsDashboard::class);
    $component->set('scheduledFilter', 'all');
    $chartData = $component->instance()->getChartData();

    expect($chartData['count'])->toBe(5);
});

it('sets date range to last day when setLastDay is called', function () {
    $component = Livewire::test(MetricsDashboard::class);
    $component->call('setLastDay');

    expect($component->get('startDate'))->toBe(now()->subDay()->format('Y-m-d'));
    expect($component->get('endDate'))->toBe(now()->format('Y-m-d'));
});

it('sets date range to last week when setLastWeek is called', function () {
    $component = Livewire::test(MetricsDashboard::class);
    $component->call('setLastWeek');

    expect($component->get('startDate'))->toBe(now()->subWeek()->format('Y-m-d'));
    expect($component->get('endDate'))->toBe(now()->format('Y-m-d'));
});

it('sets date range to last month when setLastMonth is called', function () {
    $component = Livewire::test(MetricsDashboard::class);
    $component->call('setLastMonth');

    expect($component->get('startDate'))->toBe(now()->subMonth()->format('Y-m-d'));
    expect($component->get('endDate'))->toBe(now()->format('Y-m-d'));
});

it('dispatches charts-updated event when setLastDay is called', function () {
    Result::factory()->create([
        'created_at' => now()->subHours(12),
    ]);

    $component = Livewire::test(MetricsDashboard::class);
    $component->call('setLastDay');

    $component->assertDispatched('charts-updated');
});

it('dispatches charts-updated event when setLastWeek is called', function () {
    Result::factory()->create([
        'created_at' => now()->subDays(3),
    ]);

    $component = Livewire::test(MetricsDashboard::class);
    $component->call('setLastWeek');

    $component->assertDispatched('charts-updated');
});

it('dispatches charts-updated event when setLastMonth is called', function () {
    Result::factory()->create([
        'created_at' => now()->subDays(15),
    ]);

    $component = Livewire::test(MetricsDashboard::class);
    $component->call('setLastMonth');

    $component->assertDispatched('charts-updated');
});

it('uses DEFAULT_CHART_RANGE config for default start date', function () {
    config(['speedtest.default_chart_range' => '7d']);

    $component = Livewire::test(MetricsDashboard::class);

    expect($component->get('startDate'))->toBe(now()->subDays(7)->format('Y-m-d'));
    expect($component->get('endDate'))->toBe(now()->format('Y-m-d'));
});

it('parses hours from DEFAULT_CHART_RANGE config', function () {
    config(['speedtest.default_chart_range' => '48h']);

    $component = Livewire::test(MetricsDashboard::class);

    expect($component->get('startDate'))->toBe(now()->subHours(48)->format('Y-m-d'));
});

it('parses weeks from DEFAULT_CHART_RANGE config', function () {
    config(['speedtest.default_chart_range' => '2w']);

    $component = Livewire::test(MetricsDashboard::class);

    expect($component->get('startDate'))->toBe(now()->subWeeks(2)->format('Y-m-d'));
});

it('parses months from DEFAULT_CHART_RANGE config', function () {
    config(['speedtest.default_chart_range' => '3m']);

    $component = Livewire::test(MetricsDashboard::class);

    expect($component->get('startDate'))->toBe(now()->subMonths(3)->format('Y-m-d'));
});

it('defaults to 1 day when DEFAULT_CHART_RANGE config is invalid', function () {
    config(['speedtest.default_chart_range' => 'invalid']);

    $component = Livewire::test(MetricsDashboard::class);

    expect($component->get('startDate'))->toBe(now()->subDay()->format('Y-m-d'));
});

it('checks for new results and refreshes charts when new result is added', function () {
    $initialResult = Result::factory()->create([
        'created_at' => now()->subHours(2),
    ]);

    $component = Livewire::test(MetricsDashboard::class);

    // Create a new result
    $newResult = Result::factory()->create([
        'created_at' => now()->subHours(1),
    ]);

    // Call checkForNewResults
    $component->call('checkForNewResults');

    // Should dispatch charts-updated event
    $component->assertDispatched('charts-updated');
});

it('does not refresh charts when no new results exist', function () {
    Result::factory()->create([
        'created_at' => now()->subHours(1),
    ]);

    $component = Livewire::test(MetricsDashboard::class);

    // Call checkForNewResults without adding new results
    $component->call('checkForNewResults');

    // Should not dispatch charts-updated event (no new results)
    $component->assertNotDispatched('charts-updated');
});

it('updates lastResultId when new result is detected', function () {
    $initialResult = Result::factory()->create([
        'created_at' => now()->subHours(2),
    ]);

    $component = Livewire::test(MetricsDashboard::class);
    expect($component->get('lastResultId'))->toBe($initialResult->id);

    // Create a new result
    $newResult = Result::factory()->create([
        'created_at' => now()->subHours(1),
    ]);

    // Call checkForNewResults
    $component->call('checkForNewResults');

    // lastResultId should be updated
    expect($component->get('lastResultId'))->toBe($newResult->id);
});

it('has failed results when download benchmark fails', function () {
    Result::factory()->create([
        'created_at' => now()->subHours(1),
        'benchmarks' => [
            'download' => ['bar' => 'min', 'passed' => false, 'type' => 'absolute', 'value' => 100, 'unit' => 'mbps'],
        ],
    ]);

    $component = Livewire::test(MetricsDashboard::class);
    $chartData = $component->instance()->getChartData();

    expect($chartData['hasFailedResults'])->toBeTrue();
});

it('has failed results when upload benchmark fails', function () {
    Result::factory()->create([
        'created_at' => now()->subHours(1),
        'benchmarks' => [
            'upload' => ['bar' => 'min', 'passed' => false, 'type' => 'absolute', 'value' => 50, 'unit' => 'mbps'],
        ],
    ]);

    $component = Livewire::test(MetricsDashboard::class);
    $chartData = $component->instance()->getChartData();

    expect($chartData['hasFailedResults'])->toBeTrue();
});

it('has failed results when ping benchmark fails', function () {
    Result::factory()->create([
        'created_at' => now()->subHours(1),
        'benchmarks' => [
            'ping' => ['bar' => 'max', 'passed' => false, 'type' => 'absolute', 'value' => 100, 'unit' => 'ms'],
        ],
    ]);

    $component = Livewire::test(MetricsDashboard::class);
    $chartData = $component->instance()->getChartData();

    expect($chartData['hasFailedResults'])->toBeTrue();
});

it('does not have failed results when all benchmarks pass', function () {
    Result::factory()->count(3)->create([
        'created_at' => now()->subHours(1),
        'benchmarks' => [
            'download' => ['bar' => 'min', 'passed' => true, 'type' => 'absolute', 'value' => 100, 'unit' => 'mbps'],
            'upload' => ['bar' => 'min', 'passed' => true, 'type' => 'absolute', 'value' => 50, 'unit' => 'mbps'],
            'ping' => ['bar' => 'max', 'passed' => true, 'type' => 'absolute', 'value' => 100, 'unit' => 'ms'],
        ],
    ]);

    $component = Livewire::test(MetricsDashboard::class);
    $chartData = $component->instance()->getChartData();

    expect($chartData['hasFailedResults'])->toBeFalse();
});

it('does not have failed results when no results exist', function () {
    $component = Livewire::test(MetricsDashboard::class);
    $chartData = $component->instance()->getChartData();

    expect($chartData['hasFailedResults'])->toBeFalse();
});

it('has failed results when at least one result fails even if others pass', function () {
    // Create passing results
    Result::factory()->count(2)->create([
        'created_at' => now()->subHours(3),
        'benchmarks' => [
            'download' => ['bar' => 'min', 'passed' => true, 'type' => 'absolute', 'value' => 100, 'unit' => 'mbps'],
            'upload' => ['bar' => 'min', 'passed' => true, 'type' => 'absolute', 'value' => 50, 'unit' => 'mbps'],
            'ping' => ['bar' => 'max', 'passed' => true, 'type' => 'absolute', 'value' => 100, 'unit' => 'ms'],
        ],
    ]);

    // Create one failing result
    Result::factory()->create([
        'created_at' => now()->subHours(1),
        'benchmarks' => [
            'download' => ['bar' => 'min', 'passed' => false, 'type' => 'absolute', 'value' => 100, 'unit' => 'mbps'],
        ],
    ]);

    $component = Livewire::test(MetricsDashboard::class);
    $chartData = $component->instance()->getChartData();

    expect($chartData['hasFailedResults'])->toBeTrue();
});

it('includes results with failed status in chart data', function () {
    Result::factory()->create([
        'created_at' => now()->subHours(2),
        'status' => ResultStatus::Completed,
        'download' => 125000000,
    ]);

    Result::factory()->create([
        'created_at' => now()->subHours(1),
        'status' => ResultStatus::Failed,
    ]);

    $component = Livewire::test(MetricsDashboard::class);
    $chartData = $component->instance()->getChartData();

    expect($chartData['count'])->toBe(2);
    expect($chartData['resultStatusFailed'])->toHaveCount(2);
});

it('sets download and upload to 0 for failed status results', function () {
    Result::factory()->create([
        'created_at' => now()->subHours(2),
        'status' => ResultStatus::Completed,
        'download' => 125000000,
        'upload' => 50000000,
    ]);

    Result::factory()->create([
        'created_at' => now()->subHours(1),
        'status' => ResultStatus::Failed,
        'download' => 125000000,
        'upload' => 50000000,
    ]);

    $component = Livewire::test(MetricsDashboard::class);
    $chartData = $component->instance()->getChartData();

    // First result (completed) should have normal values
    expect($chartData['download'][0])->toBe(1000.0);
    expect($chartData['upload'][0])->toBe(400.0);

    // Second result (failed) should be 0
    expect($chartData['download'][1])->toBe(0.0);
    expect($chartData['upload'][1])->toBe(0.0);
});

it('tracks failed status results separately from benchmark failures', function () {
    // Completed result that failed benchmark
    Result::factory()->create([
        'created_at' => now()->subHours(2),
        'status' => ResultStatus::Completed,
        'benchmarks' => [
            'download' => ['bar' => 'min', 'passed' => false, 'type' => 'absolute', 'value' => 100, 'unit' => 'mbps'],
        ],
    ]);

    // Failed status result
    Result::factory()->create([
        'created_at' => now()->subHours(1),
        'status' => ResultStatus::Failed,
    ]);

    $component = Livewire::test(MetricsDashboard::class);
    $chartData = $component->instance()->getChartData();

    expect($chartData['resultStatusFailed'][0])->toBeFalse();
    expect($chartData['resultStatusFailed'][1])->toBeTrue();
    expect($chartData['downloadBenchmarkFailed'][0])->toBeTrue();
});

it('sets ping to 0 for failed status results', function () {
    Result::factory()->create([
        'created_at' => now()->subHours(2),
        'status' => ResultStatus::Completed,
        'ping' => 25.5,
    ]);

    Result::factory()->create([
        'created_at' => now()->subHours(1),
        'status' => ResultStatus::Failed,
        'ping' => 50.0,
    ]);

    $component = Livewire::test(MetricsDashboard::class);
    $chartData = $component->instance()->getChartData();

    // First result (completed) should have normal ping value
    expect($chartData['ping'][0])->toBe(25.5);

    // Second result (failed) should be 0
    expect($chartData['ping'][1])->toBe(0.0);
});

it('sets latency to 0 for failed status results', function () {
    Result::factory()->create([
        'created_at' => now()->subHours(2),
        'status' => ResultStatus::Completed,
        'data' => [
            'download' => ['latency' => ['iqm' => 12.5]],
            'upload' => ['latency' => ['iqm' => 18.3]],
        ],
    ]);

    Result::factory()->create([
        'created_at' => now()->subHours(1),
        'status' => ResultStatus::Failed,
        'data' => [
            'download' => ['latency' => ['iqm' => 30.0]],
            'upload' => ['latency' => ['iqm' => 40.0]],
        ],
    ]);

    $component = Livewire::test(MetricsDashboard::class);
    $chartData = $component->instance()->getChartData();

    // First result (completed) should have normal latency values
    expect($chartData['downloadLatency'][0])->toBe(12.5);
    expect($chartData['uploadLatency'][0])->toBe(18.3);

    // Second result (failed) should be 0
    expect($chartData['downloadLatency'][1])->toBe(0.0);
    expect($chartData['uploadLatency'][1])->toBe(0.0);
});

it('sets jitter to 0 for failed status results', function () {
    Result::factory()->create([
        'created_at' => now()->subHours(2),
        'status' => ResultStatus::Completed,
        'data' => [
            'download' => ['jitter' => 2.5],
            'upload' => ['jitter' => 3.2],
            'ping' => ['jitter' => 1.8],
        ],
    ]);

    Result::factory()->create([
        'created_at' => now()->subHours(1),
        'status' => ResultStatus::Failed,
        'data' => [
            'download' => ['jitter' => 5.0],
            'upload' => ['jitter' => 6.0],
            'ping' => ['jitter' => 4.0],
        ],
    ]);

    $component = Livewire::test(MetricsDashboard::class);
    $chartData = $component->instance()->getChartData();

    // First result (completed) should have normal jitter values
    expect($chartData['downloadJitter'][0])->toBe(2.5);
    expect($chartData['uploadJitter'][0])->toBe(3.2);
    expect($chartData['pingJitter'][0])->toBe(1.8);

    // Second result (failed) should be 0
    expect($chartData['downloadJitter'][1])->toBe(0.0);
    expect($chartData['uploadJitter'][1])->toBe(0.0);
    expect($chartData['pingJitter'][1])->toBe(0.0);
});

it('includes all jitter datasets when date range is updated', function () {
    Result::factory()->create([
        'created_at' => now()->subHours(2),
        'data' => [
            'download' => ['jitter' => 2.5],
            'upload' => ['jitter' => 3.2],
            'ping' => ['jitter' => 1.8],
        ],
    ]);

    $component = Livewire::test(MetricsDashboard::class);

    // Update the date range
    $component->set('startDate', now()->subDay()->format('Y-m-d'));

    // Verify the dispatched event includes all three jitter datasets
    $component->assertDispatched('charts-updated', function ($event) {
        $chartData = $event['chartData'];

        return isset($chartData['downloadJitter'])
            && isset($chartData['uploadJitter'])
            && isset($chartData['pingJitter'])
            && is_array($chartData['downloadJitter'])
            && is_array($chartData['uploadJitter'])
            && is_array($chartData['pingJitter']);
    });
});

it('includes all jitter datasets when setLastDay is called', function () {
    Result::factory()->create([
        'created_at' => now()->subHours(12),
        'data' => [
            'download' => ['jitter' => 1.5],
            'upload' => ['jitter' => 2.0],
            'ping' => ['jitter' => 1.2],
        ],
    ]);

    $component = Livewire::test(MetricsDashboard::class);
    $component->call('setLastDay');

    // Verify the dispatched event includes all three jitter datasets
    $component->assertDispatched('charts-updated', function ($event) {
        $chartData = $event['chartData'];

        return isset($chartData['downloadJitter'])
            && isset($chartData['uploadJitter'])
            && isset($chartData['pingJitter'])
            && count($chartData['downloadJitter']) === 1
            && count($chartData['uploadJitter']) === 1
            && count($chartData['pingJitter']) === 1
            && $chartData['downloadJitter'][0] === 1.5
            && $chartData['uploadJitter'][0] === 2.0
            && $chartData['pingJitter'][0] === 1.2;
    });
});
