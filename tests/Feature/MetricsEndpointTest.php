<?php

use App\Models\Result;
use App\Services\PrometheusMetricsService;
use App\Settings\DataIntegrationSettings;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

describe('metrics endpoint', function () {
    test('returns 404 when prometheus is disabled', function () {
        app(DataIntegrationSettings::class)->fill(['prometheus_enabled' => false])->save();

        $response = $this->get('/prometheus');

        $response->assertNotFound();
    });

    test('returns metrics when prometheus is enabled and no IP restrictions', function () {
        app(DataIntegrationSettings::class)->fill([
            'prometheus_enabled' => true,
            'prometheus_allowed_ips' => [],
        ])->save();

        $result = Result::factory()->create();

        // Simulate the listener updating metrics
        app(PrometheusMetricsService::class)->updateMetrics($result);

        $response = $this->get('/prometheus');

        $response->assertSuccessful();
        $response->assertHeader('Content-Type', 'text/plain; version=0.0.4; charset=utf-8');
    });

    test('returns 403 when IP is not in allowed list', function () {
        app(DataIntegrationSettings::class)->fill([
            'prometheus_enabled' => true,
            'prometheus_allowed_ips' => ['192.168.1.100', '10.0.0.1'],
        ])->save();

        $response = $this->get('/prometheus', [
            'REMOTE_ADDR' => '192.168.1.50',
        ]);

        $response->assertForbidden();
    });

    test('returns metrics when IP is in allowed list', function () {
        app(DataIntegrationSettings::class)->fill([
            'prometheus_enabled' => true,
            'prometheus_allowed_ips' => ['192.168.1.100', '10.0.0.1'],
        ])->save();

        $result = Result::factory()->create();

        // Simulate the listener updating metrics
        app(PrometheusMetricsService::class)->updateMetrics($result);

        $response = $this->get('/prometheus', [
            'REMOTE_ADDR' => '192.168.1.100',
        ]);

        $response->assertSuccessful();
        $response->assertHeader('Content-Type', 'text/plain; version=0.0.4; charset=utf-8');
    });

    test('allows access with empty array', function () {
        app(DataIntegrationSettings::class)->fill([
            'prometheus_enabled' => true,
            'prometheus_allowed_ips' => [],
        ])->save();

        $result = Result::factory()->create();

        // Simulate the listener updating metrics
        app(PrometheusMetricsService::class)->updateMetrics($result);

        $response = $this->get('/prometheus', [
            'REMOTE_ADDR' => '10.0.0.1',
        ]);

        $response->assertSuccessful();
    });

    test('allows access when IP is in CIDR range', function () {
        app(DataIntegrationSettings::class)->fill([
            'prometheus_enabled' => true,
            'prometheus_allowed_ips' => ['192.168.1.0/24'],
        ])->save();

        $result = Result::factory()->create();

        // Simulate the listener updating metrics
        app(PrometheusMetricsService::class)->updateMetrics($result);

        $response = $this->get('/prometheus', [
            'REMOTE_ADDR' => '192.168.1.150',
        ]);

        $response->assertSuccessful();
    });

    test('denies access when IP is not in CIDR range', function () {
        app(DataIntegrationSettings::class)->fill([
            'prometheus_enabled' => true,
            'prometheus_allowed_ips' => ['192.168.1.0/24'],
        ])->save();

        $response = $this->get('/prometheus', [
            'REMOTE_ADDR' => '192.168.2.1',
        ]);

        $response->assertForbidden();
    });

    test('supports mixed IP addresses and CIDR ranges', function () {
        app(DataIntegrationSettings::class)->fill([
            'prometheus_enabled' => true,
            'prometheus_allowed_ips' => ['10.0.0.1', '192.168.1.0/24'],
        ])->save();

        $result = Result::factory()->create();

        // Simulate the listener updating metrics
        app(PrometheusMetricsService::class)->updateMetrics($result);

        $response = $this->get('/prometheus', [
            'REMOTE_ADDR' => '192.168.1.50',
        ]);

        $response->assertSuccessful();

        $response = $this->get('/prometheus', [
            'REMOTE_ADDR' => '10.0.0.1',
        ]);

        $response->assertSuccessful();
    });

    test('completed result emits expected metric names in response body', function () {
        app(DataIntegrationSettings::class)->fill([
            'prometheus_enabled' => true,
            'prometheus_allowed_ips' => [],
        ])->save();

        $result = Result::factory()->create([
            'status' => \App\Enums\ResultStatus::Completed,
            'download' => 115625000,
            'upload' => 113750000,
        ]);

        app(PrometheusMetricsService::class)->updateMetrics($result);

        $response = $this->get('/prometheus');

        $response->assertSuccessful();
        $response->assertSee('speedtest_tracker_up');
        $response->assertSee('speedtest_tracker_info');
        $response->assertSee('speedtest_tracker_download_bytes_per_second');
        $response->assertSee('speedtest_tracker_upload_bytes_per_second');
        $response->assertSee('speedtest_tracker_download_bits_per_second');
        $response->assertSee('speedtest_tracker_upload_bits_per_second');
    });

    test('failed result does not emit speed gauge metrics', function () {
        app(DataIntegrationSettings::class)->fill([
            'prometheus_enabled' => true,
            'prometheus_allowed_ips' => [],
        ])->save();

        $result = Result::factory()->create([
            'status' => \App\Enums\ResultStatus::Failed,
            'download' => null,
            'upload' => null,
            'ping' => null,
            'data' => ['type' => 'log', 'level' => 'error', 'message' => 'Test error.', 'timestamp' => '2024-03-01T01:00:00Z'],
        ]);

        app(PrometheusMetricsService::class)->updateMetrics($result);

        $response = $this->get('/prometheus');

        $response->assertSuccessful();
        $response->assertSee('speedtest_tracker_up');
        $response->assertSee('speedtest_tracker_info');
        $response->assertDontSee('speedtest_tracker_download_bytes_per_second');
        $response->assertDontSee('speedtest_tracker_upload_bytes_per_second');
        $response->assertDontSee('speedtest_tracker_download_bits_per_second');
        $response->assertDontSee('speedtest_tracker_upload_bits_per_second');
    });

    test('generateMetrics returns up and build_info metrics when no cache exists', function () {
        $metrics = app(PrometheusMetricsService::class)->generateMetrics();

        expect($metrics)
            ->toContain('speedtest_tracker_up 1')
            ->toContain('speedtest_tracker_build_info');
    });
});
