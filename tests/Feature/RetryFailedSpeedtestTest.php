<?php

use App\Events\SpeedtestFailed;
use App\Jobs\Ookla\RunSpeedtestJob;
use App\Listeners\RetryFailedSpeedtest;
use App\Models\Result;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Bus::fake();
});

describe('RetryFailedSpeedtest', function () {
    it('delays by the configured retry delay', function () {
        Config::set('speedtest.retry_delay', 42);

        $listener = new RetryFailedSpeedtest;

        expect($listener->delay)->toBe(42);
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
