<?php

use App\Listeners\RunScheduledSpeedtest;
use App\Models\Result;
use App\Models\Speedtest;
use App\Models\User;
use DirectoryTree\Cadence\Events\ScheduleTriggered;
use DirectoryTree\Cadence\Schedule as CadenceSchedule;
use Illuminate\Support\Facades\Bus;

describe('RunScheduledSpeedtest listener', function () {
    it('dispatches a speedtest batch when a speedtest schedule is triggered', function () {
        Bus::fake();

        $speedtest = Speedtest::factory()->create();

        (new RunScheduledSpeedtest)->handle(new ScheduleTriggered($speedtest->cronSchedule()));

        Bus::assertBatched(fn ($batch) => $batch->name === 'Ookla Speedtest');

        expect(Result::query()->where('speedtest_id', $speedtest->id)->count())->toBe(1)
            ->and(Result::query()->first()->scheduled)->toBeTrue();
    });

    it('does nothing when the triggered schedule belongs to a non-speedtest model', function () {
        Bus::fake();

        $user = User::factory()->create();
        $schedule = CadenceSchedule::create([
            'schedulable_type' => User::class,
            'schedulable_id' => $user->id,
            'type' => 'cron',
            'expression' => '* * * * *',
            'next_run_at' => now(),
        ]);

        (new RunScheduledSpeedtest)->handle(new ScheduleTriggered($schedule));

        Bus::assertNothingBatched();
    });
});
