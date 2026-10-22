<?php

use App\Actions\CheckForScheduledSpeedtests;
use App\Actions\Speedtests\RunSpeedtest;
use App\Enums\ResultService;
use App\Enums\ResultStatus;
use App\Jobs\Ookla\SelectSpeedtestServerJob;
use App\Jobs\Speedtests\RunSpeedtestJob;
use App\Models\Result;
use App\Services\ScheduledSpeedtestService;
use App\Settings\SpeedtestSettings;
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
    $settings = app(SpeedtestSettings::class);
    $settings->iperf3_servers = ['iperf.example.com:5201'];
    $settings->save();

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

test('schedules from the settings run their own service and target', function () {
    $settings = app(SpeedtestSettings::class);
    $settings->schedules = [
        ['enabled' => true, 'service' => 'yandex', 'cron' => '* * * * *', 'target' => null],
        ['enabled' => true, 'service' => 'file', 'cron' => '* * * * *', 'target' => 'https://example.com/100MB.bin'],
        ['enabled' => false, 'service' => 'cloudflare', 'cron' => '* * * * *', 'target' => null],
        ['enabled' => true, 'service' => 'iperf3', 'cron' => '0 0 1 1 *', 'target' => 'iperf.example.com'],
    ];
    $settings->save();

    CheckForScheduledSpeedtests::run();

    $results = Result::orderBy('id')->get();

    expect($results)->toHaveCount(2)
        ->and($results[0]->service)->toBe(ResultService::Yandex)
        ->and($results[1]->service)->toBe(ResultService::File)
        ->and($results[1]->server_host)->toBe('https://example.com/100MB.bin')
        ->and($results->every->scheduled)->toBeTrue();
});

test('the environment schedule runs next to the settings schedules', function () {
    config(['speedtest.schedule' => '* * * * *']);

    $settings = app(SpeedtestSettings::class);
    $settings->schedules = [['enabled' => true, 'service' => 'cloudflare', 'cron' => '* * * * *', 'target' => null]];
    $settings->save();

    CheckForScheduledSpeedtests::run();

    expect(Result::pluck('service')->all())->toEqualCanonicalizing([ResultService::Ookla, ResultService::Cloudflare]);
});

test('the next scheduled speedtest is the earliest of all schedules', function () {
    config(['speedtest.schedule' => null, 'app.display_timezone' => 'UTC']);

    $settings = app(SpeedtestSettings::class);
    $settings->schedules = [
        ['enabled' => true, 'service' => 'yandex', 'cron' => '0 0 * * *', 'target' => null],
        ['enabled' => true, 'service' => 'cloudflare', 'cron' => '*/5 * * * *', 'target' => null],
        ['enabled' => false, 'service' => 'file', 'cron' => '* * * * *', 'target' => null],
    ];
    $settings->save();

    expect(ScheduledSpeedtestService::getNextScheduledTest()->minute % 5)->toBe(0)
        ->and(ScheduledSpeedtestService::getNextScheduledTest()->isBefore(now()->addMinutes(6)))->toBeTrue();
});

test('scheduled speedtests fall back to ookla for an unknown service', function () {
    config([
        'speedtest.schedule' => '* * * * *',
        'speedtest.service' => 'nope',
    ]);

    CheckForScheduledSpeedtests::run();

    expect(Result::sole()->service)->toBe(ResultService::Ookla);
});
