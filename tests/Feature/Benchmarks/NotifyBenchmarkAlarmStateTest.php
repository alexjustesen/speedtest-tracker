<?php

use App\Enums\BenchmarkMetric;
use App\Events\BenchmarkAlarmsRecovered;
use App\Events\BenchmarkAlarmsTriggered;
use App\Mail\BenchmarkAlarmMail;
use App\Models\Benchmark;
use App\Models\Result;
use App\Settings\NotificationSettings;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    // The migration seeds one fixed row per metric; start each test clean.
    Benchmark::query()->delete();

    app(NotificationSettings::class)->fill([
        'mail_enabled' => true,
        'mail_recipients' => ['ops@example.com'],
        'mail_on_benchmark_failure' => false,
        'mail_on_benchmark_recovery' => false,
    ])->save();
});

it('sends a mail notification when a benchmark is triggered and the failure toggle is enabled', function () {
    Mail::fake();

    app(NotificationSettings::class)->fill(['mail_on_benchmark_failure' => true])->save();

    $result = Result::factory()->create(['scheduled' => true]);
    $benchmark = Benchmark::factory()->inAlarm()->create(['metric' => BenchmarkMetric::Download]);

    event(new BenchmarkAlarmsTriggered($result, new Collection([$benchmark])));

    Mail::assertQueued(BenchmarkAlarmMail::class, fn (BenchmarkAlarmMail $mail) => $mail->hasTo('ops@example.com'));
});

it('does not send a mail notification when the failure toggle is disabled', function () {
    Mail::fake();

    $result = Result::factory()->create(['scheduled' => true]);
    $benchmark = Benchmark::factory()->inAlarm()->create(['metric' => BenchmarkMetric::Download]);

    event(new BenchmarkAlarmsTriggered($result, new Collection([$benchmark])));

    Mail::assertNothingOutgoing();
});

it('sends a recovery mail notification only when the recovery toggle is enabled', function () {
    Mail::fake();

    app(NotificationSettings::class)->fill(['mail_on_benchmark_recovery' => true])->save();

    $result = Result::factory()->create(['scheduled' => true]);
    $benchmark = Benchmark::factory()->create(['metric' => BenchmarkMetric::Download]);

    event(new BenchmarkAlarmsRecovered($result, new Collection([$benchmark])));

    Mail::assertQueued(BenchmarkAlarmMail::class);
});

it('does not notify for unscheduled results', function () {
    Mail::fake();

    app(NotificationSettings::class)->fill(['mail_on_benchmark_failure' => true])->save();

    $result = Result::factory()->create(['scheduled' => false]);
    $benchmark = Benchmark::factory()->inAlarm()->create(['metric' => BenchmarkMetric::Download]);

    event(new BenchmarkAlarmsTriggered($result, new Collection([$benchmark])));

    Mail::assertNothingOutgoing();
});
