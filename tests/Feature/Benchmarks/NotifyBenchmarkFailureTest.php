<?php

use App\Events\SpeedtestBenchmarkUnhealthy;
use App\Mail\BenchmarkAlarmMail;
use App\Models\Result;
use App\Settings\NotificationSettings;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    app(NotificationSettings::class)->fill([
        'mail_enabled' => true,
        'mail_recipients' => ['ops@example.com'],
        'mail_on_benchmark_failure' => false,
    ])->save();
});

it('sends a mail notification when a benchmark fails and the failure toggle is enabled', function () {
    Mail::fake();

    app(NotificationSettings::class)->fill(['mail_on_benchmark_failure' => true])->save();

    $result = Result::factory()->create(['scheduled' => true]);

    event(new SpeedtestBenchmarkUnhealthy($result));

    Mail::assertQueued(BenchmarkAlarmMail::class, fn (BenchmarkAlarmMail $mail) => $mail->hasTo('ops@example.com'));
});

it('does not send a mail notification when the failure toggle is disabled', function () {
    Mail::fake();

    $result = Result::factory()->create(['scheduled' => true]);

    event(new SpeedtestBenchmarkUnhealthy($result));

    Mail::assertNothingOutgoing();
});

it('does not notify for unscheduled results', function () {
    Mail::fake();

    app(NotificationSettings::class)->fill(['mail_on_benchmark_failure' => true])->save();

    $result = Result::factory()->create(['scheduled' => false]);

    event(new SpeedtestBenchmarkUnhealthy($result));

    Mail::assertNothingOutgoing();
});
