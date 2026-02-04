<?php

use App\Models\Result;
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

        Result::factory()->create();

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

        Result::factory()->create();

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

        Result::factory()->create();

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

        Result::factory()->create();

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

        Result::factory()->create();

        $response = $this->get('/prometheus', [
            'REMOTE_ADDR' => '192.168.1.50',
        ]);

        $response->assertSuccessful();

        $response = $this->get('/prometheus', [
            'REMOTE_ADDR' => '10.0.0.1',
        ]);

        $response->assertSuccessful();
    });

    test('handles results with missing packet loss data', function () {
        app(DataIntegrationSettings::class)->fill([
            'prometheus_enabled' => true,
            'prometheus_allowed_ips' => [],
        ])->save();

        // Create a result without packet loss data
        $dataWithoutPacketLoss = json_decode('{"isp": "Speedtest Communications", "ping": {"low": 17.841, "high": 24.077, "jitter": 1.878, "latency": 19.133}, "type": "result", "result": {"id": "d6fe2fb3-f4f8-4cc5-b898-7b42109e67c2", "url": "https://docs.speedtest-tracker.dev", "persisted": true}, "server": {"id": 0, "ip": "127.0.0.1", "host": "docs.speedtest-tracker.dev", "name": "Speedtest", "port": 8080, "country": "United States", "location": "New York City, NY"}, "upload": {"bytes": 124297377, "elapsed": 9628, "latency": {"iqm": 341.111, "low": 16.663, "high": 529.86, "jitter": 37.587}, "bandwidth": 113750000}, "download": {"bytes": 230789788, "elapsed": 14301, "latency": {"iqm": 104.125, "low": 23.72, "high": 269.563, "jitter": 13.447}, "bandwidth": 115625000}, "interface": {"name": "eth0", "isVpn": false, "macAddr": "00:00:00:00:00:00", "externalIp": "127.0.0.1", "internalIp": "127.0.0.1"}, "timestamp": "2024-03-01T01:00:00Z"}', true);

        Result::factory()->create([
            'ping' => $dataWithoutPacketLoss['ping']['latency'],
            'download' => $dataWithoutPacketLoss['download']['bandwidth'],
            'upload' => $dataWithoutPacketLoss['upload']['bandwidth'],
            'data' => $dataWithoutPacketLoss,
        ]);

        $response = $this->get('/prometheus');

        $response->assertSuccessful();
        $response->assertHeader('Content-Type', 'text/plain; version=0.0.4; charset=utf-8');
        // Verify packet_loss metric is not in the output when data is missing
        expect($response->getContent())->not->toContain('speedtest_tracker_packet_loss_percent');
    });

    test('handles failed speedtests by only exporting info metric', function () {
        app(DataIntegrationSettings::class)->fill([
            'prometheus_enabled' => true,
            'prometheus_allowed_ips' => [],
        ])->save();

        // Create a failed result
        $failedData = json_decode('{"type": "log", "level": "error", "message": "Connection timeout", "timestamp": "2024-03-01T01:00:00Z"}', true);

        $result = Result::factory()->create([
            'status' => \App\Enums\ResultStatus::Failed,
            'data' => $failedData,
        ]);

        // Cache the result ID so the Prometheus service can find it
        Cache::forever('prometheus:latest_result', $result->id);

        $response = $this->get('/prometheus');

        $response->assertSuccessful();
        $response->assertHeader('Content-Type', 'text/plain; version=0.0.4; charset=utf-8');

        $content = $response->getContent();

        // Should have the info metric (result_id)
        expect($content)->toContain('speedtest_tracker_result_id');

        // Should NOT have numeric metrics for failed tests
        expect($content)->not->toContain('speedtest_tracker_download_bytes');
        expect($content)->not->toContain('speedtest_tracker_upload_bytes');
        expect($content)->not->toContain('speedtest_tracker_ping_ms');
        expect($content)->not->toContain('speedtest_tracker_packet_loss_percent');
    });
});
