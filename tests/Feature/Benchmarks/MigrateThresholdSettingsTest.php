<?php

use App\Enums\BenchmarkMetric;
use App\Models\Benchmark;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Set a threshold setting value directly in the settings table, as if the
 * old CreateThresholdsSettings migration had run on a previous version.
 */
function setThresholdSetting(string $name, mixed $value): void
{
    DB::table('settings')->updateOrInsert(
        ['group' => 'threshold', 'name' => $name],
        ['payload' => json_encode($value), 'locked' => false, 'updated_at' => now()],
    );
}

function runCreateBenchmarksTableMigration(): void
{
    Schema::dropIfExists('benchmarks');

    $migration = require base_path('database/migrations/2026_08_26_164457_create_benchmarks_table.php');
    $migration->up();
}

it('seeds enabled benchmarks from existing absolute threshold settings', function () {
    setThresholdSetting('absolute_enabled', true);
    setThresholdSetting('absolute_download', 100.0);
    setThresholdSetting('absolute_upload', 50.0);
    setThresholdSetting('absolute_ping', 30.0);

    runCreateBenchmarksTableMigration();

    expect(Benchmark::count())->toBe(4);

    $download = Benchmark::where('metric', BenchmarkMetric::Download)->sole();

    expect($download->enabled)->toBeTrue()
        ->and($download->absolute_value)->toBe(100.0);

    $packetLoss = Benchmark::where('metric', BenchmarkMetric::PacketLoss)->sole();

    expect($packetLoss->enabled)->toBeFalse();

    expect(DB::table('settings')->where('group', 'threshold')->count())->toBe(0);
});

it('leaves every metric disabled when no threshold settings existed', function () {
    runCreateBenchmarksTableMigration();

    expect(Benchmark::count())->toBe(4)
        ->and(Benchmark::enabled()->count())->toBe(0);
});

it('does not enable a metric whose old threshold value was zero', function () {
    setThresholdSetting('absolute_enabled', true);
    setThresholdSetting('absolute_download', 0);
    setThresholdSetting('absolute_upload', 50.0);
    setThresholdSetting('absolute_ping', 0);

    runCreateBenchmarksTableMigration();

    expect(Benchmark::where('metric', BenchmarkMetric::Download)->sole()->enabled)->toBeFalse()
        ->and(Benchmark::where('metric', BenchmarkMetric::Upload)->sole()->enabled)->toBeTrue();
});
