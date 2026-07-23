<?php

use App\Models\Result;
use App\Models\Speedtest;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;

describe('schedules:run', function () {
    it('dispatches a speedtest batch for a due speedtest schedule', function () {
        Bus::fake();

        $speedtest = Speedtest::factory()->withCron('* * * * *')->create();

        // Cadence computes next_run_at as the next occurrence strictly after creation time,
        // so force it into the past to simulate a schedule that's actually due right now.
        $speedtest->cronSchedule()->forceFill(['next_run_at' => now()->subMinute()])->save();

        Artisan::call('schedules:run');

        Bus::assertBatched(fn ($batch) => $batch->name === 'Ookla Speedtest');

        expect(Result::query()->where('speedtest_id', $speedtest->id)->count())->toBe(1);
    });

    it('does not dispatch for a speedtest schedule that is not due', function () {
        Bus::fake();

        // Cron expression that only runs at 23:59 on December 31 (a Sunday) — effectively never during tests
        Speedtest::factory()->withCron('59 23 31 12 0')->create();

        Artisan::call('schedules:run');

        Bus::assertNothingBatched();
    });

    it('does not dispatch for a disabled speedtest schedule', function () {
        Bus::fake();

        Speedtest::factory()->withCron('* * * * *')->disabled()->create();

        Artisan::call('schedules:run');

        Bus::assertNothingBatched();
    });
});
