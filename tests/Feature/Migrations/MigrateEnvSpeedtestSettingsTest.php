<?php

use App\Enums\ServerMode;
use App\Models\Speedtest;
use Illuminate\Support\Facades\DB;

function importSpeedtestEnv(array $env): void
{
    $migration = require base_path('database/migrations/2026_05_30_080250_migrate_env_speedtest_settings_to_speedtests_table.php');

    $migration->importFromEnv(...array_merge([
        'expression' => null,
        'servers' => null,
    ], $env));
}

describe('env speedtest settings migration', function () {
    it('creates a default speedtest with an enabled cron schedule', function () {
        importSpeedtestEnv([
            'expression' => '0 * * * *',
            'servers' => '111, 222',
        ]);

        $speedtest = Speedtest::with('schedule')->sole();

        expect($speedtest->name)->toBe('Default')
            ->and($speedtest->server_mode)->toBe(ServerMode::Prefer)
            ->and($speedtest->servers)->toBe(['111', '222'])
            ->and($speedtest->schedule->expression)->toBe('0 * * * *')
            ->and($speedtest->schedule->isEnabled())->toBeTrue()
            ->and($speedtest->schedule->next_run_at)->not->toBeNull();
    });

    it('uses automatic mode without servers', function () {
        importSpeedtestEnv(['expression' => '0 * * * *']);

        expect(Speedtest::sole())
            ->server_mode->toBe(ServerMode::Auto)
            ->servers->toBeNull();
    });

    it('skips blank and invalid cron expressions', function (?string $expression) {
        importSpeedtestEnv(['expression' => $expression]);

        expect(DB::table('speedtests')->count())->toBe(0)
            ->and(DB::table('schedules')->count())->toBe(0);
    })->with([null, '', 'not a cron']);

    it('does not import when a speedtest already exists', function () {
        Speedtest::factory()->create();

        importSpeedtestEnv(['expression' => '0 * * * *']);

        expect(DB::table('speedtests')->count())->toBe(1);
    });
});
