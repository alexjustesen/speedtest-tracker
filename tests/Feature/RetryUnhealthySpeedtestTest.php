<?php

use App\Events\SpeedtestBenchmarkUnhealthy;
use App\Jobs\Ookla\RunSpeedtestJob;
use App\Listeners\RetryUnhealthySpeedtest;
use App\Models\Result;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Bus::fake();
});

describe('RetryUnhealthySpeedtest', function () {
    it('delays by the configured retry delay', function () {
        Config::set('speedtest.retry_delay', 42);

        $listener = new RetryUnhealthySpeedtest;

        expect($listener->delay)->toBe(42);
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
