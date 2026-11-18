<?php

use App\Events\SpeedtestFailed;
use App\Jobs\Ookla\RunSpeedtestJob;
use App\Listeners\RetryFailedSpeedtest;
use App\Models\Result;
use Illuminate\Bus\PendingBatch;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Bus::fake();
});

describe('RetryFailedSpeedtest', function () {
    it('delays by the configured retry delay', function () {
        Config::set('speedtest.retry_delay', 42);

        $event = new SpeedtestFailed(Result::factory()->create(['scheduled' => true]), 1);

        expect((new RetryFailedSpeedtest)->withDelay($event))->toBe(42);
    });

    it('clamps a negative retry delay to zero', function () {
        Config::set('speedtest.retry_delay', -5);

        $event = new SpeedtestFailed(Result::factory()->create(['scheduled' => true]), 1);

        expect((new RetryFailedSpeedtest)->withDelay($event))->toBe(0);
    });

    it('queues the listener with the configured delay when the event is dispatched for real', function () {
        Config::set('speedtest.retry_delay', 7);
        Config::set('speedtest.retry_times', 3);

        // QueueFake::laterOn() discards its $delay argument, so the real
        // delay can't be observed through Queue::fake(). Mock the resolved
        // connection instead to capture what the dispatcher actually sends.
        $capturedDelay = null;
        $connection = Mockery::mock();
        $connection->shouldReceive('laterOn')
            ->once()
            ->withArgs(function ($queue, $delay, $job) use (&$capturedDelay) {
                $capturedDelay = $delay;

                return $job instanceof CallQueuedListener && $job->class === RetryFailedSpeedtest::class;
            });
        Queue::shouldReceive('connection')->andReturn($connection);

        SpeedtestFailed::dispatch(Result::factory()->create(['scheduled' => true]), 1);

        expect($capturedDelay)->toBe(7);
    });

    it('should be queued when retries remain', function () {
        Config::set('speedtest.retry_times', 3);

        $event = new SpeedtestFailed(Result::factory()->create(['scheduled' => true]), 1);

        expect((new RetryFailedSpeedtest)->shouldQueue($event))->toBeTrue();
    });

    it('should not be queued when retries are disabled', function () {
        Config::set('speedtest.retry_times', 0);

        $event = new SpeedtestFailed(Result::factory()->create(['scheduled' => true]), 1);

        expect((new RetryFailedSpeedtest)->shouldQueue($event))->toBeFalse();
    });

    it('dispatches another attempt when retries remain', function () {
        Config::set('speedtest.retry_times', 3);

        $result = Result::factory()->create(['scheduled' => true]);
        $event = new SpeedtestFailed($result, 1);

        (new RetryFailedSpeedtest)->handle($event);

        Bus::assertBatched(function (PendingBatch $batch) {
            return $batch->jobs->flatten()->first(fn ($job) => $job instanceof RunSpeedtestJob)?->attempt === 2;
        });
    });

    it('does not dispatch when retries are disabled', function () {
        Config::set('speedtest.retry_times', 0);

        $result = Result::factory()->create(['scheduled' => true]);
        $event = new SpeedtestFailed($result, 1);

        (new RetryFailedSpeedtest)->handle($event);

        Bus::assertNothingBatched();
    });
});
