<?php

use App\Actions\Ookla\RetrySpeedtest;
use App\Jobs\Ookla\RunSpeedtestJob;
use App\Models\Result;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Bus::fake();
});

describe('RetrySpeedtest', function () {
    it('does not dispatch another attempt when retries are disabled', function () {
        Config::set('speedtest.retry_times', 0);

        $result = Result::factory()->create(['scheduled' => true]);

        RetrySpeedtest::run($result, 1);

        Bus::assertNothingBatched();
    });

    it('dispatches the next attempt when attempts remain', function () {
        Config::set('speedtest.retry_times', 3);

        $result = Result::factory()->create(['scheduled' => true]);

        RetrySpeedtest::run($result, 1);

        Bus::assertBatched(function (PendingBatch $batch) {
            return $batch->jobs->flatten()->first(fn ($job) => $job instanceof RunSpeedtestJob)?->attempt === 2;
        });
    });

    it('does not dispatch once attempts are exhausted', function () {
        Config::set('speedtest.retry_times', 2);

        $result = Result::factory()->create(['scheduled' => true]);

        RetrySpeedtest::run($result, 3);

        Bus::assertNothingBatched();
    });
});
