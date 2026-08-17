<?php

use App\Enums\ResultStatus;
use App\Events\SpeedtestFailed;
use App\Jobs\Ookla\SkipSpeedtestJob;
use App\Models\Result;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Event::fake();
});

describe('SkipSpeedtestJob', function () {
    test('defaults to attempt 1 when not specified', function () {
        $result = Result::factory()->create();

        $job = new SkipSpeedtestJob($result);

        expect($job->attempt)->toBe(1);
    });

    test('accepts and stores a specific attempt number', function () {
        $result = Result::factory()->create();

        $job = new SkipSpeedtestJob($result, 2);

        expect($job->attempt)->toBe(2);
    });

    test('dispatches SpeedtestFailed with the current attempt number when the external IP lookup fails', function () {
        Config::set('speedtest.preflight.skip_ips', '1.2.3.4');

        $result = Result::factory()->create(['scheduled' => true]);

        Http::fake([
            '*' => Http::response('', 503),
        ]);

        [$job, $batch] = (new SkipSpeedtestJob($result, 4))->withFakeBatch();
        $job->handle();

        $this->assertTrue($batch->cancelled());
        $result->refresh();
        expect($result->status)->toBe(ResultStatus::Failed);
        Event::assertDispatched(SpeedtestFailed::class, function (SpeedtestFailed $event) {
            return $event->attempt === 4;
        });
    });
});
