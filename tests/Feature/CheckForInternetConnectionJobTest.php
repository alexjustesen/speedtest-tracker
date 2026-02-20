<?php

use App\Actions\PingHostname;
use App\Enums\ResultStatus;
use App\Events\SpeedtestFailed;
use App\Jobs\CheckForInternetConnectionJob;
use App\Models\Result;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Spatie\Ping\PingResult;

beforeEach(function () {
    Event::fake();
});

describe('CheckForInternetConnectionJob', function () {
    test('batch continues when ping succeeds', function () {
        $result = Result::factory()->create(['status' => ResultStatus::Started]);

        $successfulPing = PingResult::fromArray(['success' => true, 'host' => 'icanhazip.com']);
        app()->bind(PingHostname::class, fn () => new class($successfulPing)
        {
            public function __construct(private PingResult $ping) {}

            public function handle(?string $hostname = null, int $count = 1): ?PingResult
            {
                return $this->ping;
            }
        });

        [$job, $batch] = (new CheckForInternetConnectionJob($result))->withFakeBatch();
        $job->handle();

        $this->assertFalse($batch->cancelled());
        $result->refresh();
        expect($result->status)->toBe(ResultStatus::Checking);
        Event::assertNotDispatched(SpeedtestFailed::class);
    });

    test('batch continues when ping fails but HTTP fallback succeeds', function () {
        $result = Result::factory()->create(['status' => ResultStatus::Started]);

        $failedPing = PingResult::fromArray([
            'success' => false,
            'error' => 'hostUnreachable',
            'host' => 'icanhazip.com',
        ]);
        app()->bind(PingHostname::class, fn () => new class($failedPing)
        {
            public function __construct(private PingResult $ping) {}

            public function handle(?string $hostname = null, int $count = 1): ?PingResult
            {
                return $this->ping;
            }
        });

        Http::fake([
            '*' => Http::response('1.2.3.4', 200),
        ]);

        [$job, $batch] = (new CheckForInternetConnectionJob($result))->withFakeBatch();
        $job->handle();

        $this->assertFalse($batch->cancelled());
        $result->refresh();
        expect($result->status)->toBe(ResultStatus::Checking);
        Event::assertNotDispatched(SpeedtestFailed::class);
    });

    test('batch continues when ping is unavailable but HTTP fallback succeeds', function () {
        $result = Result::factory()->create(['status' => ResultStatus::Started]);

        app()->bind(PingHostname::class, fn () => new class
        {
            public function handle(?string $hostname = null, int $count = 1): ?PingResult
            {
                return null;
            }
        });

        Http::fake([
            '*' => Http::response('1.2.3.4', 200),
        ]);

        [$job, $batch] = (new CheckForInternetConnectionJob($result))->withFakeBatch();
        $job->handle();

        $this->assertFalse($batch->cancelled());
        $result->refresh();
        expect($result->status)->toBe(ResultStatus::Checking);
        Event::assertNotDispatched(SpeedtestFailed::class);
    });

    test('batch is cancelled when ping fails and HTTP fallback also fails', function () {
        $result = Result::factory()->create(['status' => ResultStatus::Started]);

        $failedPing = PingResult::fromArray([
            'success' => false,
            'error' => 'hostUnreachable',
            'host' => 'icanhazip.com',
        ]);
        app()->bind(PingHostname::class, fn () => new class($failedPing)
        {
            public function __construct(private PingResult $ping) {}

            public function handle(?string $hostname = null, int $count = 1): ?PingResult
            {
                return $this->ping;
            }
        });

        Http::fake([
            '*' => Http::response('Service Unavailable', 503),
        ]);

        [$job, $batch] = (new CheckForInternetConnectionJob($result))->withFakeBatch();
        $job->handle();

        $this->assertTrue($batch->cancelled());
        $result->refresh();
        expect($result->status)->toBe(ResultStatus::Failed);
        expect($result->data['level'])->toBe('error');
        expect($result->data['message'])->toContain('HTTP fallback also failed');
        Event::assertDispatched(SpeedtestFailed::class);
    });

    test('batch is cancelled when ping is unavailable and HTTP fallback throws', function () {
        $result = Result::factory()->create(['status' => ResultStatus::Started]);

        app()->bind(PingHostname::class, fn () => new class
        {
            public function handle(?string $hostname = null, int $count = 1): ?PingResult
            {
                return null;
            }
        });

        Http::fake([
            '*' => Http::failedConnection(),
        ]);

        [$job, $batch] = (new CheckForInternetConnectionJob($result))->withFakeBatch();
        $job->handle();

        $this->assertTrue($batch->cancelled());
        $result->refresh();
        expect($result->status)->toBe(ResultStatus::Failed);
        expect($result->data['message'])->toBe('Ping command is unavailable and HTTP fallback also failed.');
        Event::assertDispatched(SpeedtestFailed::class);
    });
});
