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
});
