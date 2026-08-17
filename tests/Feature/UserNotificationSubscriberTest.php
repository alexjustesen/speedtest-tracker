<?php

use App\Events\SpeedtestBenchmarkUnhealthy;
use App\Events\SpeedtestFailed;
use App\Listeners\UserNotificationSubscriber;
use App\Models\Result;
use App\Models\User;
use Filament\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();
});

describe('UserNotificationSubscriber::handleFailed', function () {
    it('does not notify for an attempt that will still be retried', function () {
        Config::set('speedtest.retry_times', 3);

        $user = User::factory()->create();
        $result = Result::factory()->create(['scheduled' => true, 'dispatched_by' => $user->id]);
        $event = new SpeedtestFailed($result, 1);

        (new UserNotificationSubscriber)->handleFailed($event);

        Notification::assertNothingSentTo($user);
    });

    it('notifies on the final attempt when retries are disabled', function () {
        Config::set('speedtest.retry_times', 0);

        $user = User::factory()->create();
        $result = Result::factory()->create(['scheduled' => true, 'dispatched_by' => $user->id]);
        $event = new SpeedtestFailed($result, 1);

        (new UserNotificationSubscriber)->handleFailed($event);

        Notification::assertSentTo($user, DatabaseNotification::class);
    });
});

describe('UserNotificationSubscriber::handleBenchmarkFailed', function () {
    it('does not notify for an attempt that will still be retried', function () {
        Config::set('speedtest.retry_times', 3);

        $user = User::factory()->create();
        $result = Result::factory()->create(['scheduled' => true, 'dispatched_by' => $user->id]);
        $event = new SpeedtestBenchmarkUnhealthy($result, 1);

        (new UserNotificationSubscriber)->handleBenchmarkFailed($event);

        Notification::assertNothingSentTo($user);
    });

    it('notifies on the final attempt when retries are disabled', function () {
        Config::set('speedtest.retry_times', 0);

        $user = User::factory()->create();
        $result = Result::factory()->create(['scheduled' => true, 'dispatched_by' => $user->id]);
        $event = new SpeedtestBenchmarkUnhealthy($result, 1);

        (new UserNotificationSubscriber)->handleBenchmarkFailed($event);

        Notification::assertSentTo($user, DatabaseNotification::class);
    });
});
