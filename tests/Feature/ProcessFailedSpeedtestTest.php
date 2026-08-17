<?php

use App\Events\SpeedtestFailed;
use App\Listeners\ProcessFailedSpeedtest;
use App\Models\Result;
use Illuminate\Support\Facades\Config;

describe('ProcessFailedSpeedtest', function () {
    it('returns early without error for an attempt that will still be retried', function () {
        Config::set('speedtest.retry_times', 3);

        $result = Result::factory()->create(['scheduled' => true]);
        $event = new SpeedtestFailed($result, 1);

        expect(fn () => (new ProcessFailedSpeedtest)->handle($event))->not->toThrow(Throwable::class);
    });

    it('returns without error on the final attempt when retries are disabled', function () {
        Config::set('speedtest.retry_times', 0);

        $result = Result::factory()->create(['scheduled' => true]);
        $event = new SpeedtestFailed($result, 1);

        expect(fn () => (new ProcessFailedSpeedtest)->handle($event))->not->toThrow(Throwable::class);
    });
});
