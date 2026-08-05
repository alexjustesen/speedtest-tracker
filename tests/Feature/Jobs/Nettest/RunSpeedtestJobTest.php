<?php

use App\Enums\ResultService;
use App\Enums\ResultStatus;
use App\Events\SpeedtestFailed;
use App\Jobs\Nettest\RunSpeedtestJob;
use App\Models\Result;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;

describe('Nettest RunSpeedtestJob', function () {
    it('fails the result with a helpful message when no server is configured', function () {
        Config::set('speedtest.nettest.server', null);
        Event::fake();

        $result = Result::create([
            'service' => ResultService::Nettest,
            'status' => ResultStatus::Waiting,
        ]);

        [$job, $batch] = (new RunSpeedtestJob($result))->withFakeBatch();

        $job->handle();

        $result->refresh();

        expect($result->status)->toBe(ResultStatus::Failed);
        expect($result->data['message'])->toContain('NETTEST_SERVER');
        expect($batch->cancelled())->toBeTrue();

        Event::assertDispatched(SpeedtestFailed::class);
    });
});
