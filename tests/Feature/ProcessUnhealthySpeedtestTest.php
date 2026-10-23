<?php

use App\Events\SpeedtestBenchmarkUnhealthy;
use App\Listeners\ProcessUnhealthySpeedtest;
use App\Mail\UnhealthySpeedtestMail;
use App\Models\Result;
use App\Models\User;
use App\Settings\NotificationSettings;
use Filament\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $settings = app(NotificationSettings::class);
    $settings->database_enabled = true;
    $settings->database_on_threshold_failure = true;
    $settings->mail_enabled = true;
    $settings->mail_on_threshold_failure = true;
    $settings->mail_recipients = ['alerts@example.com'];
    $settings->save();

    Mail::fake();
    Notification::fake();
});

describe('ProcessUnhealthySpeedtest', function () {
    it('does not notify for an attempt that will still be retried', function () {
        Config::set('speedtest.retry_times', 3);

        $user = User::factory()->create();
        $result = Result::factory()->create(['scheduled' => true]);
        $event = new SpeedtestBenchmarkUnhealthy($result, 1);

        app(ProcessUnhealthySpeedtest::class)->handle($event);

        Mail::assertNothingOutgoing();
        Notification::assertNothingSentTo($user);
    });

    it('does not notify when the attempt equals the retry limit', function () {
        Config::set('speedtest.retry_times', 3);

        $user = User::factory()->create();
        $result = Result::factory()->create(['scheduled' => true]);
        $event = new SpeedtestBenchmarkUnhealthy($result, 3);

        app(ProcessUnhealthySpeedtest::class)->handle($event);

        Mail::assertNothingOutgoing();
        Notification::assertNothingSentTo($user);
    });

    it('notifies on the final attempt when retries are disabled', function () {
        Config::set('speedtest.retry_times', 0);

        $user = User::factory()->create();
        $result = Result::factory()->create(['scheduled' => true]);
        $event = new SpeedtestBenchmarkUnhealthy($result, 1);

        app(ProcessUnhealthySpeedtest::class)->handle($event);

        Mail::assertQueued(UnhealthySpeedtestMail::class);
        Notification::assertSentTo($user, DatabaseNotification::class);
    });
});
