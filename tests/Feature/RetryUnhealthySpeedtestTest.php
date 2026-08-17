<?php

use App\Events\SpeedtestBenchmarkUnhealthy;
use App\Jobs\Ookla\RunSpeedtestJob;
use App\Listeners\RetryUnhealthySpeedtest;
use App\Models\Result;
use Illuminate\Bus\PendingBatch;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Bus::fake();
});

describe('RetryUnhealthySpeedtest', function () {
    it('delays by the configured retry delay', function () {
        Config::set('speedtest.retry_delay', 42);

        $event = new SpeedtestBenchmarkUnhealthy(Result::factory()->create(['scheduled' => true]), 1);

        expect((new RetryUnhealthySpeedtest)->withDelay($event))->toBe(42);
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

                return $job instanceof CallQueuedListener && $job->class === RetryUnhealthySpeedtest::class;
            });
        Queue::shouldReceive('connection')->andReturn($connection);

        SpeedtestBenchmarkUnhealthy::dispatch(Result::factory()->create(['scheduled' => true]), 1);

        expect($capturedDelay)->toBe(7);
    });

    it('should be queued when retries remain', function () {
        Config::set('speedtest.retry_times', 3);

        $event = new SpeedtestBenchmarkUnhealthy(Result::factory()->create(['scheduled' => true]), 1);

        expect((new RetryUnhealthySpeedtest)->shouldQueue($event))->toBeTrue();
    });

    it('should not be queued when retries are disabled', function () {
        Config::set('speedtest.retry_times', 0);

        $event = new SpeedtestBenchmarkUnhealthy(Result::factory()->create(['scheduled' => true]), 1);

        expect((new RetryUnhealthySpeedtest)->shouldQueue($event))->toBeFalse();
    });

    it('dispatches another attempt when retries remain', function () {
        Config::set('speedtest.retry_times', 3);

        $result = Result::factory()->create(['scheduled' => true]);
        $event = new SpeedtestBenchmarkUnhealthy($result, 1);

        (new RetryUnhealthySpeedtest)->handle($event);

        Bus::assertBatched(function (PendingBatch $batch) {
            return $batch->jobs->flatten()->first(fn ($job) => $job instanceof RunSpeedtestJob)?->attempt === 2;
        });
    });

    it('does not dispatch when retries are disabled', function () {
        Config::set('speedtest.retry_times', 0);

        $result = Result::factory()->create(['scheduled' => true]);
        $event = new SpeedtestBenchmarkUnhealthy($result, 1);

        (new RetryUnhealthySpeedtest)->handle($event);

        Bus::assertNothingBatched();
    });
});
