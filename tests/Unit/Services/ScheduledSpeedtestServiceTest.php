<?php

use App\Services\ScheduledSpeedtestService;
use Carbon\Carbon;

test('returns null when schedule config is null', function () {
    config()->set('speedtest.schedule', collect());

    $result = ScheduledSpeedtestService::getNextScheduledTest();

    expect($result)->toBeNull();
});

test('returns null when schedule config is false', function () {
    config()->set('speedtest.schedule', collect());

    $result = ScheduledSpeedtestService::getNextScheduledTest();

    expect($result)->toBeNull();
});

test('returns null when schedule config is blank string', function () {
    config()->set('speedtest.schedule', collect());

    $result = ScheduledSpeedtestService::getNextScheduledTest();

    expect($result)->toBeNull();
});

test('returns Carbon instance when schedule is configured', function () {
    config()->set('speedtest.schedule', collect(['*/5 * * * *'])); // Every 5 minutes

    $result = ScheduledSpeedtestService::getNextScheduledTest();

    expect($result)->toBeInstanceOf(Carbon::class);
});

test('returns Carbon instance when schedule is configured with multiple values', function () {
    config()->set('speedtest.schedule', collect(['*/5 * * * *', '*/12 * * * *'])); // Every 5 minutes

    $result = ScheduledSpeedtestService::getNextScheduledTest();

    expect($result)->toBeInstanceOf(Carbon::class);
});

test('returns correct next scheduled time for hourly cron', function () {
    config()->set('speedtest.schedule', collect(['0 * * * *'])); // Every hour at minute 0
    config()->set('app.display_timezone', 'UTC');

    $result = ScheduledSpeedtestService::getNextScheduledTest();

    expect($result)->toBeInstanceOf(Carbon::class);
    expect($result->minute)->toBe(0);
});

test('returns correct next scheduled time for daily cron', function () {
    config()->set('speedtest.schedule', collect(['0 0 * * *'])); // Every day at midnight
    config()->set('app.display_timezone', 'UTC');

    $result = ScheduledSpeedtestService::getNextScheduledTest();

    expect($result)->toBeInstanceOf(Carbon::class);
    expect($result->hour)->toBe(0);
    expect($result->minute)->toBe(0);
});

test('returns future date for next scheduled test', function () {
    config()->set('speedtest.schedule', collect(['*/5 * * * *'])); // Every 5 minutes
    config()->set('app.display_timezone', 'UTC');

    $result = ScheduledSpeedtestService::getNextScheduledTest();

    expect($result)->toBeInstanceOf(Carbon::class);
    expect($result->isFuture())->toBeTrue();
});
