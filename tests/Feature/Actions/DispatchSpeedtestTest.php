<?php

use App\Actions\DispatchSpeedtest;
use App\Enums\ResultService;
use App\Models\Result;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;

describe('DispatchSpeedtest', function () {
    it('runs a speedtest with ookla by default', function () {
        Bus::fake();
        Event::fake();

        $result = DispatchSpeedtest::run();

        expect($result)->toBeInstanceOf(Result::class);
        expect($result->service)->toBe(ResultService::Ookla);
    });

    it('runs a speedtest with nettest when it is the configured service', function () {
        Config::set('speedtest.service', 'nettest');
        Bus::fake();
        Event::fake();

        $result = DispatchSpeedtest::run();

        expect($result->service)->toBe(ResultService::Nettest);
    });

    it('falls back to ookla when the configured service is unknown', function () {
        Config::set('speedtest.service', 'nope');

        expect(DispatchSpeedtest::service())->toBe(ResultService::Ookla);
    });
});
