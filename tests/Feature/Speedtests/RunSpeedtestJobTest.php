<?php

use App\Enums\ResultService;
use App\Enums\ResultStatus;
use App\Events\SpeedtestFailed;
use App\Jobs\Ookla\BenchmarkSpeedtestJob;
use App\Jobs\Speedtests\RunSpeedtestJob;
use App\Models\Result;
use App\Services\Speedtests\Contracts\Speedtest;
use App\Services\Speedtests\FileDownloadSpeedtest;
use App\Services\Speedtests\YandexSpeedtest;
use App\Settings\ThresholdSettings;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake();
});

function fakeSpeedtest(string $class, Closure $run): void
{
    app()->bind($class, fn () => new class($run) implements Speedtest
    {
        public function __construct(private Closure $run) {}

        public function run(?string $target = null): array
        {
            return ($this->run)($target);
        }
    });
}

test('the speedtest output is stored on the result', function () {
    fakeSpeedtest(YandexSpeedtest::class, fn () => [
        'type' => 'result',
        'ping' => ['latency' => 12.5, 'jitter' => 1.2, 'low' => 11, 'high' => 15],
        'download' => ['bandwidth' => 12_500_000, 'bytes' => 125_000_000, 'elapsed' => 10_000],
        'upload' => ['bandwidth' => 5_000_000, 'bytes' => 50_000_000, 'elapsed' => 10_000],
        'server' => ['name' => 'Yandex', 'location' => 'FRA', 'host' => 'cloudcdn-fra-02.cdn.yandex.net'],
    ]);

    $result = Result::factory()->create(['service' => ResultService::Yandex, 'status' => ResultStatus::Started, 'data' => []]);

    [$job, $batch] = (new RunSpeedtestJob($result))->withFakeBatch();
    $job->handle();

    $result->refresh();

    expect($batch->cancelled())->toBeFalse()
        ->and($result->ping)->toEqual(12.5)
        ->and($result->download)->toEqual(12_500_000)
        ->and($result->upload)->toEqual(5_000_000)
        ->and($result->download_bytes)->toEqual(125_000_000)
        ->and($result->server_name)->toBe('Yandex')
        ->and($result->server_location)->toBe('FRA');
});

test('the target is passed to the speedtest', function () {
    $received = null;

    fakeSpeedtest(FileDownloadSpeedtest::class, function (?string $target) use (&$received) {
        $received = $target;

        return ['download' => ['bandwidth' => 1, 'bytes' => 1, 'elapsed' => 1]];
    });

    $result = Result::factory()->create([
        'service' => ResultService::File,
        'status' => ResultStatus::Started,
        'data' => ['server' => ['host' => 'https://example.com/100MB.bin']],
    ]);

    (new RunSpeedtestJob($result))->handle();

    expect($received)->toBe('https://example.com/100MB.bin')
        ->and($result->refresh()->upload)->toBeNull();
});

test('a failing speedtest marks the result as failed and cancels the batch', function () {
    fakeSpeedtest(YandexSpeedtest::class, fn () => throw new RuntimeException('Unable to retrieve Yandex Internetometer probes.'));

    $result = Result::factory()->create(['service' => ResultService::Yandex, 'status' => ResultStatus::Started, 'data' => []]);

    [$job, $batch] = (new RunSpeedtestJob($result))->withFakeBatch();
    $job->handle();

    $result->refresh();

    expect($batch->cancelled())->toBeTrue()
        ->and($result->status)->toBe(ResultStatus::Failed)
        ->and($result->error_message)->toBe('Unable to retrieve Yandex Internetometer probes.');

    Event::assertDispatched(SpeedtestFailed::class);
});

test('a result without upload is only benchmarked on what was measured', function () {
    $settings = app(ThresholdSettings::class);
    $settings->absolute_enabled = true;
    $settings->absolute_download = 100;
    $settings->absolute_upload = 50;
    $settings->absolute_ping = 0;
    $settings->save();

    $result = Result::factory()->create([
        'service' => ResultService::File,
        'download' => 25_000_000,
        'upload' => null,
        'upload_bytes' => null,
    ]);

    (new BenchmarkSpeedtestJob($result))->handle();

    $result->refresh();

    expect($result->benchmarks)->toHaveKey('download')
        ->and($result->benchmarks)->not->toHaveKey('upload')
        ->and($result->healthy)->toBeTrue();
});
