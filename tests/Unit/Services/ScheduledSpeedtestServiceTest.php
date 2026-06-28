<?php

use App\Services\ScheduledSpeedtestService;
use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('returns null when schedule config is null', function () {
    config()->set('speedtest.schedule', null);

    $result = ScheduledSpeedtestService::getNextScheduledTest();

    expect($result)->toBeNull();
});

test('returns null when schedule config is false', function () {
    config()->set('speedtest.schedule', false);

    $result = ScheduledSpeedtestService::getNextScheduledTest();

    expect($result)->toBeNull();
});

test('returns null when schedule config is blank string', function () {
    config()->set('speedtest.schedule', '');

    $result = ScheduledSpeedtestService::getNextScheduledTest();

    expect($result)->toBeNull();
});

test('returns Carbon instance when schedule is configured', function () {
    config()->set('speedtest.schedule', '*/5 * * * *'); // Every 5 minutes

    $result = ScheduledSpeedtestService::getNextScheduledTest();

    expect($result)->toBeInstanceOf(Carbon::class);
});

test('returns correct next scheduled time for hourly cron', function () {
    config()->set('speedtest.schedule', '0 * * * *'); // Every hour at minute 0
    app(GeneralSettings::class)->fill(['display_timezone' => 'UTC'])->save();

    $result = ScheduledSpeedtestService::getNextScheduledTest();

    expect($result)->toBeInstanceOf(Carbon::class);
    expect($result->minute)->toBe(0);
});

test('returns correct next scheduled time for daily cron', function () {
    config()->set('speedtest.schedule', '0 0 * * *'); // Every day at midnight
    app(GeneralSettings::class)->fill(['display_timezone' => 'UTC'])->save();

    $result = ScheduledSpeedtestService::getNextScheduledTest();

    expect($result)->toBeInstanceOf(Carbon::class);
    expect($result->hour)->toBe(0);
    expect($result->minute)->toBe(0);
});

test('returns future date for next scheduled test', function () {
    config()->set('speedtest.schedule', '*/5 * * * *'); // Every 5 minutes
    app(GeneralSettings::class)->fill(['display_timezone' => 'UTC'])->save();

    $result = ScheduledSpeedtestService::getNextScheduledTest();

    expect($result)->toBeInstanceOf(Carbon::class);
    expect($result->isFuture())->toBeTrue();
});
