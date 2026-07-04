<?php

use App\Enums\ResultStatus;
use App\Events\SpeedtestFailed;
use App\Events\SpeedtestSkipped;
use App\Jobs\Ookla\SkipSpeedtestJob;
use App\Models\Result;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Event::fake();
});

describe('SkipSpeedtestJob', function () {
    test('batch is cancelled and error message is stored when external IP lookup fails', function () {
        config(['speedtest.preflight.skip_ips' => '1.2.3.4']);

        Http::fake([
            '*' => Http::response('', 500),
        ]);

        $result = Result::factory()->create(['status' => ResultStatus::Started, 'scheduled' => true]);

        [$job, $batch] = (new SkipSpeedtestJob($result))->withFakeBatch();
        $job->handle();

        $this->assertTrue($batch->cancelled());
        $result->refresh();
        expect($result->status)->toBe(ResultStatus::Failed);
        expect($result->error_message)->not->toBeNull();
        Event::assertDispatched(SpeedtestFailed::class);
    });

    test('batch is cancelled and result is skipped when external IP matches skip list', function () {
        config(['speedtest.preflight.skip_ips' => '1.2.3.4']);

        Http::fake([
            '*' => Http::response('1.2.3.4', 200),
        ]);

        $result = Result::factory()->create(['status' => ResultStatus::Started, 'scheduled' => true]);

        [$job, $batch] = (new SkipSpeedtestJob($result))->withFakeBatch();
        $job->handle();

        $this->assertTrue($batch->cancelled());
        $result->refresh();
        expect($result->status)->toBe(ResultStatus::Skipped);
        expect($result->ip_address)->toBe('1.2.3.4');
        expect($result->error_message)->toContain('was found in external IP address skip list');
        Event::assertDispatched(SpeedtestSkipped::class);
    });
});
