<?php

use App\Models\Schedule;
use App\Services\ScheduledSpeedtestService;
use Carbon\Carbon;

test('returns null when no schedules exist', function () {
    $result = ScheduledSpeedtestService::getNextScheduledTest();

    expect($result)->toBeNull();
});

test('returns null when all schedules are disabled', function () {
    Schedule::factory()->disabled()->create(['schedule' => '0 * * * *']);

    $result = ScheduledSpeedtestService::getNextScheduledTest();

    expect($result)->toBeNull();
});

test('returns Carbon instance when an enabled schedule exists', function () {
    Schedule::factory()->create(['schedule' => '*/5 * * * *']);

    $result = ScheduledSpeedtestService::getNextScheduledTest();

    expect($result)->toBeInstanceOf(Carbon::class);
});

test('returns future date for next scheduled test', function () {
    config()->set('app.display_timezone', 'UTC');
    Schedule::factory()->create(['schedule' => '*/5 * * * *']);

    $result = ScheduledSpeedtestService::getNextScheduledTest();

    expect($result)->toBeInstanceOf(Carbon::class)
        ->and($result->isFuture())->toBeTrue();
});

test('returns the soonest next run time when multiple schedules exist', function () {
    config()->set('app.display_timezone', 'UTC');

    Schedule::factory()->create(['schedule' => '0 0 * * *']); // daily at midnight
    Schedule::factory()->create(['schedule' => '* * * * *']);  // every minute

    $result = ScheduledSpeedtestService::getNextScheduledTest();

    // The every-minute schedule is always sooner, so result should be within 1 minute
    expect($result)->toBeInstanceOf(Carbon::class)
        ->and($result->diffInMinutes(now()))->toBeLessThanOrEqual(1);
});

test('ignores disabled schedules when finding next run', function () {
    config()->set('app.display_timezone', 'UTC');

    Schedule::factory()->disabled()->create(['schedule' => '* * * * *']); // disabled every-minute
    Schedule::factory()->create(['schedule' => '0 0 * * *']);             // enabled daily at midnight

    $result = ScheduledSpeedtestService::getNextScheduledTest();

    expect($result)->toBeInstanceOf(Carbon::class)
        ->and($result->hour)->toBe(0)
        ->and($result->minute)->toBe(0);
});
