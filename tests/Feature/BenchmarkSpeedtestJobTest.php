<?php

use App\Events\SpeedtestBenchmarkUnhealthy;
use App\Jobs\Ookla\BenchmarkSpeedtestJob;
use App\Models\Result;
use App\Settings\ThresholdSettings;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake();
});

describe('BenchmarkSpeedtestJob', function () {
    test('defaults to attempt 1 when not specified', function () {
        $result = Result::factory()->create();

        $job = new BenchmarkSpeedtestJob($result);

        expect($job->attempt)->toBe(1);
    });

    test('accepts and stores a specific attempt number', function () {
        $result = Result::factory()->create();

        $job = new BenchmarkSpeedtestJob($result, 2);

        expect($job->attempt)->toBe(2);
    });

    test('dispatches SpeedtestBenchmarkUnhealthy with the current attempt number', function () {
        $settings = app(ThresholdSettings::class);
        $settings->absolute_enabled = true;
        $settings->absolute_download = 100;
        $settings->save();

        $result = Result::factory()->create([
            'download' => 1,
        ]);

        $job = new BenchmarkSpeedtestJob($result, 3);
        $job->handle();

        Event::assertDispatched(SpeedtestBenchmarkUnhealthy::class, function (SpeedtestBenchmarkUnhealthy $event) {
            return $event->attempt === 3;
        });
    });
});
