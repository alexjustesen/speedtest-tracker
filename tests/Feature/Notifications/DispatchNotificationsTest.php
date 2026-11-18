<?php

use App\Enums\NotificationEvent;
use App\Enums\ResultStatus;
use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestFailed;
use App\Events\SpeedtestSkipped;
use App\Jobs\SendChannelNotification;
use App\Models\NotificationChannel;
use App\Models\Result;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    Bus::fake([SendChannelNotification::class]);
});

it('queues only enabled channels subscribed to the fired event', function () {
    $subscribed = NotificationChannel::factory()->events(NotificationEvent::Completed)->create();
    NotificationChannel::factory()->events(NotificationEvent::Failed)->create();
    NotificationChannel::factory()->disabled()->events(NotificationEvent::Completed)->create();

    SpeedtestCompleted::dispatch(Result::factory()->create(['scheduled' => true]));

    Bus::assertDispatchedTimes(SendChannelNotification::class, 1);
    Bus::assertDispatched(SendChannelNotification::class, fn (SendChannelNotification $job) => $job->channel->is($subscribed)
        && $job->event === NotificationEvent::Completed);
});

it('skips unscheduled results unless the channel includes them', function () {
    $included = NotificationChannel::factory()->webhook()->events(NotificationEvent::Completed)->create();
    NotificationChannel::factory()->mail()->events(NotificationEvent::Completed)->create();

    SpeedtestCompleted::dispatch(Result::factory()->create(['scheduled' => false]));

    Bus::assertDispatchedTimes(SendChannelNotification::class, 1);
    Bus::assertDispatched(SendChannelNotification::class, fn (SendChannelNotification $job) => $job->channel->is($included));
});

it('does not send a completed message for an unhealthy result already alarmed on', function () {
    NotificationChannel::factory()->mail()
        ->events(NotificationEvent::Completed, NotificationEvent::BenchmarkUnhealthy)
        ->create();
    $completedOnly = NotificationChannel::factory()->mail()->events(NotificationEvent::Completed)->create();

    SpeedtestCompleted::dispatch(Result::factory()->create(['scheduled' => true, 'healthy' => false]));

    Bus::assertDispatchedTimes(SendChannelNotification::class, 1);
    Bus::assertDispatched(SendChannelNotification::class, fn (SendChannelNotification $job) => $job->channel->is($completedOnly));
});

it('does not send a completed message when the result is a recovery the channel is notified about', function (NotificationEvent $recoveryEvent, array $previous, array $current) {
    NotificationChannel::factory()->mail()->events(NotificationEvent::Completed, $recoveryEvent)->create();
    $completedOnly = NotificationChannel::factory()->mail()->events(NotificationEvent::Completed)->create();

    Result::factory()->create($previous);

    SpeedtestCompleted::dispatch(Result::factory()->create(['scheduled' => true, ...$current]));

    Bus::assertDispatchedTimes(SendChannelNotification::class, 1);
    Bus::assertDispatched(SendChannelNotification::class, fn (SendChannelNotification $job) => $job->channel->is($completedOnly));
})->with([
    'after a failed test' => [NotificationEvent::Recovered, ['status' => ResultStatus::Failed], []],
    'after a benchmark breach' => [NotificationEvent::BenchmarkRecovered, ['healthy' => false], ['healthy' => true]],
]);

it('still sends a completed message when the result is not a recovery', function () {
    NotificationChannel::factory()->mail()
        ->events(NotificationEvent::Completed, NotificationEvent::Recovered, NotificationEvent::BenchmarkRecovered)
        ->create();

    Result::factory()->create(['healthy' => true]);

    SpeedtestCompleted::dispatch(Result::factory()->create(['scheduled' => true, 'healthy' => true]));

    Bus::assertDispatchedTimes(SendChannelNotification::class, 1);
});

it('notifies non-webhook channels about failed speedtests', function () {
    NotificationChannel::factory()->apprise()->events(NotificationEvent::Failed)->create();
    NotificationChannel::factory()->mail()->events(NotificationEvent::Failed)->create();

    SpeedtestFailed::dispatch(Result::factory()->create(['scheduled' => true]));

    Bus::assertDispatchedTimes(SendChannelNotification::class, 2);
});

it('ignores speedtest events that are not notification events', function () {
    NotificationChannel::factory()->webhook()->events(...NotificationEvent::cases())->create();

    SpeedtestSkipped::dispatch(Result::factory()->create());

    Bus::assertNotDispatched(SendChannelNotification::class);
});
