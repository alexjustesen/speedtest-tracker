<?php

use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestFailed;
use App\Jobs\Prometheus\WriteResult;
use App\Models\Result;
use App\Services\Prometheus\PrometheusRemoteWriteService;
use App\Settings\DataIntegrationSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    app(DataIntegrationSettings::class)->fill([
        'prometheus_remote_write_enabled' => false,
        'prometheus_remote_write_url' => null,
        'prometheus_remote_write_username' => null,
        'prometheus_remote_write_password' => null,
    ])->save();
});

describe('remote write listener', function () {
    test('dispatches WriteResult job when remote write is enabled on SpeedtestCompleted', function () {
        Queue::fake();

        app(DataIntegrationSettings::class)->fill([
            'prometheus_remote_write_enabled' => true,
            'prometheus_remote_write_url' => 'http://victoria-metrics:8428/api/v1/write',
        ])->save();

        $result = Result::factory()->create();

        event(new SpeedtestCompleted($result));

        Queue::assertPushed(WriteResult::class, fn ($job) => $job->result->is($result));
    });

    test('dispatches WriteResult job when remote write is enabled on SpeedtestFailed', function () {
        Queue::fake();

        app(DataIntegrationSettings::class)->fill([
            'prometheus_remote_write_enabled' => true,
            'prometheus_remote_write_url' => 'http://victoria-metrics:8428/api/v1/write',
        ])->save();

        $result = Result::factory()->create();

        event(new SpeedtestFailed($result));

        Queue::assertPushed(WriteResult::class, fn ($job) => $job->result->is($result));
    });

    test('does not dispatch WriteResult job when remote write is disabled', function () {
        Queue::fake();

        app(DataIntegrationSettings::class)->fill([
            'prometheus_remote_write_enabled' => false,
        ])->save();

        $result = Result::factory()->create();

        event(new SpeedtestCompleted($result));

        Queue::assertNotPushed(WriteResult::class);
    });
});

describe('PrometheusRemoteWriteService', function () {
    test('sends POST to configured URL with correct headers', function () {
        Http::fake(['*' => Http::response('', 204)]);

        app(DataIntegrationSettings::class)->fill([
            'prometheus_remote_write_enabled' => true,
            'prometheus_remote_write_url' => 'http://victoria-metrics:8428/api/v1/write',
            'prometheus_remote_write_username' => null,
            'prometheus_remote_write_password' => null,
        ])->save();

        $result = Result::factory()->create();

        app(PrometheusRemoteWriteService::class)->send($result);

        Http::assertSent(function ($request) {
            return $request->url() === 'http://victoria-metrics:8428/api/v1/write'
                && $request->method() === 'POST'
                && $request->header('Content-Type')[0] === 'application/x-protobuf'
                && $request->header('Content-Encoding')[0] === 'snappy'
                && $request->header('X-Prometheus-Remote-Write-Version')[0] === '0.1.0';
        });
    });

    test('sends Basic Auth when credentials are configured', function () {
        Http::fake(['*' => Http::response('', 204)]);

        app(DataIntegrationSettings::class)->fill([
            'prometheus_remote_write_enabled' => true,
            'prometheus_remote_write_url' => 'http://mimir:8080/api/v1/push',
            'prometheus_remote_write_username' => 'myorg',
            'prometheus_remote_write_password' => 'secret',
        ])->save();

        $result = Result::factory()->create();

        app(PrometheusRemoteWriteService::class)->send($result);

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization')
                && str_starts_with($request->header('Authorization')[0], 'Basic ');
        });
    });

    test('throws exception on non-2xx response', function () {
        Http::fake(['*' => Http::response('bad request', 400)]);

        app(DataIntegrationSettings::class)->fill([
            'prometheus_remote_write_enabled' => true,
            'prometheus_remote_write_url' => 'http://victoria-metrics:8428/api/v1/write',
        ])->save();

        $result = Result::factory()->create();

        expect(fn () => app(PrometheusRemoteWriteService::class)->send($result))
            ->toThrow(Exception::class);
    });

    test('buildWriteRequest produces non-empty protobuf payload', function () {
        $result = Result::factory()->create([
            'download' => 115625000,
            'upload' => 113750000,
            'ping' => 12.5,
        ]);

        $payload = app(PrometheusRemoteWriteService::class)->buildWriteRequest($result);

        expect($payload)->toBeString()->not->toBeEmpty();
    });

    test('buildWriteRequest skips null metric values', function () {
        $result = Result::factory()->create([
            'download' => null,
            'upload' => null,
            'ping' => null,
            'data' => ['type' => 'log', 'level' => 'error', 'message' => 'Test error.', 'timestamp' => '2024-03-01T01:00:00Z'],
        ]);

        // Should not throw — null values are simply skipped
        $payload = app(PrometheusRemoteWriteService::class)->buildWriteRequest($result);

        expect($payload)->toBeString()->not->toBeEmpty();
    });
});
