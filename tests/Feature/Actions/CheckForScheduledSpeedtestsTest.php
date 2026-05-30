<?php

use App\Actions\CheckForScheduledSpeedtests;
use App\Models\Result;
use App\Models\Schedule;
use Illuminate\Support\Facades\Bus;

describe('CheckForScheduledSpeedtests', function () {
    it('does nothing when no enabled schedules exist', function () {
        Bus::fake();

        CheckForScheduledSpeedtests::run();

        Bus::assertNothingBatched();
    });

    it('does nothing when all schedules are disabled', function () {
        Bus::fake();

        Schedule::factory()->disabled()->create(['schedule' => '* * * * *']);

        CheckForScheduledSpeedtests::run();

        Bus::assertNothingBatched();
    });

    it('dispatches a batch for a due schedule', function () {
        Bus::fake();

        $schedule = Schedule::factory()->create(['schedule' => '* * * * *']);

        CheckForScheduledSpeedtests::run();

        Bus::assertBatched(fn ($batch) => $batch->name === 'Ookla Speedtest');

        expect(Result::query()->where('schedule_id', $schedule->id)->count())->toBe(1);
    });

    it('does not dispatch for a schedule that is not due', function () {
        Bus::fake();

        // Cron expression that only runs at 23:59 on December 31 (a Sunday) — effectively never during tests
        Schedule::factory()->create(['schedule' => '59 23 31 12 0']);

        CheckForScheduledSpeedtests::run();

        Bus::assertNothingBatched();
    });

    it('dispatches separate batches for multiple due schedules', function () {
        Bus::fake();

        $scheduleA = Schedule::factory()->create(['schedule' => '* * * * *']);
        $scheduleB = Schedule::factory()->create(['schedule' => '* * * * *']);

        CheckForScheduledSpeedtests::run();

        Bus::assertBatchCount(2);

        expect(Result::query()->where('schedule_id', $scheduleA->id)->count())->toBe(1)
            ->and(Result::query()->where('schedule_id', $scheduleB->id)->count())->toBe(1);
    });

    it('marks dispatched results as scheduled', function () {
        Bus::fake();

        Schedule::factory()->create(['schedule' => '* * * * *']);

        CheckForScheduledSpeedtests::run();

        expect(Result::query()->first()->scheduled)->toBeTrue();
    });
});
