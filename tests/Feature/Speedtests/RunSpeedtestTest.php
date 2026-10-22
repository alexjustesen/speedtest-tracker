<?php

use App\Actions\CheckForScheduledSpeedtests;
use App\Actions\Speedtests\RunSpeedtest;
use App\Enums\ResultService;
use App\Enums\ResultStatus;
use App\Jobs\Ookla\SelectSpeedtestServerJob;
use App\Jobs\Speedtests\RunSpeedtestJob;
use App\Models\Result;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Bus::fake();
    Event::fake();
});

test('alternative services are queued with their own run job', function () {
    $result = RunSpeedtest::run(service: ResultService::Yandex, dispatchedBy: null);

    expect($result->service)->toBe(ResultService::Yandex)
        ->and($result->status)->toBe(ResultStatus::Waiting)
        ->and($result->scheduled)->toBeFalse();

    Bus::assertBatched(function (PendingBatch $batch): bool {
        $jobs = collect($batch->jobs->first())->map(fn ($job) => $job::class);

        return $batch->name === 'Yandex Internetometer Speedtest'
            && $jobs->contains(RunSpeedtestJob::class)
            && ! $jobs->contains(SelectSpeedtestServerJob::class);
    });
});

test('the target is stored as the server host', function () {
    $result = RunSpeedtest::run(service: ResultService::File, target: ' https://example.com/100MB.bin ');

    expect($result->server_host)->toBe('https://example.com/100MB.bin');
});

test('a configured target is used when none is given', function () {
    config(['speedtest.iperf3.servers' => 'iperf.example.com:5201']);

    $result = RunSpeedtest::run(service: ResultService::Iperf3, scheduled: true);

    expect($result->server_host)->toBe('iperf.example.com:5201')
        ->and($result->scheduled)->toBeTrue();
});

test('ookla speedtests keep using the ookla batch', function () {
    $result = RunSpeedtest::run(service: ResultService::Ookla, target: '16072');

    expect($result->service)->toBe(ResultService::Ookla)
        ->and($result->server_id)->toBe(16072);

    Bus::assertBatched(fn (PendingBatch $batch): bool => $batch->name === 'Ookla Speedtest');
});

test('scheduled speedtests use the configured service', function () {
    config([
        'speedtest.schedule' => '* * * * *',
        'speedtest.service' => 'cloudflare',
    ]);

    CheckForScheduledSpeedtests::run();

    expect(Result::sole())
        ->service->toBe(ResultService::Cloudflare)
        ->scheduled->toBeTrue();
});

test('scheduled speedtests fall back to ookla for an unknown service', function () {
    config([
        'speedtest.schedule' => '* * * * *',
        'speedtest.service' => 'nope',
    ]);

    CheckForScheduledSpeedtests::run();

    expect(Result::sole()->service)->toBe(ResultService::Ookla);
});
