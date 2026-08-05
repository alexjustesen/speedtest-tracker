<?php

use App\Actions\Nettest\RunSpeedtest;
use App\Enums\ResultService;
use App\Enums\ResultStatus;
use App\Events\SpeedtestWaiting;
use App\Models\Result;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;

describe('Nettest RunSpeedtest', function () {
    it('creates a waiting result for the nettest service', function () {
        Bus::fake();
        Event::fake();

        $result = RunSpeedtest::run();

        expect($result)->toBeInstanceOf(Result::class);
        expect($result->service)->toBe(ResultService::Nettest);
        expect($result->status)->toBe(ResultStatus::Waiting);
        expect($result->scheduled)->toBeFalse();

        Event::assertDispatched(SpeedtestWaiting::class);
        Bus::assertBatched(fn ($batch) => $batch->name === 'Nettest Speedtest');
    });

    it('marks the result as scheduled and records who dispatched it', function () {
        Bus::fake();
        Event::fake();

        $result = RunSpeedtest::run(scheduled: true, dispatchedBy: 1);

        expect($result->scheduled)->toBeTrue();
        expect($result->dispatched_by)->toBe(1);
    });
});
