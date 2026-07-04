<?php

use App\Data\OoklaResult;

it('maps a raw ookla json payload onto dto properties', function () {
    $payload = json_decode('{"isp": "Speedtest Communications", "ping": {"low": 17.841, "high": 24.077, "jitter": 1.878, "latency": 19.133}, "type": "result", "result": {"id": "d6fe2fb3-f4f8-4cc5-b898-7b42109e67c2", "url": "https://docs.speedtest-tracker.dev", "persisted": true}, "server": {"id": 12, "ip": "127.0.0.1", "host": "docs.speedtest-tracker.dev", "name": "Speedtest", "port": 8080, "country": "United States", "location": "New York City, NY"}, "upload": {"bytes": 124297377, "elapsed": 9628, "latency": {"iqm": 341.111, "low": 16.663, "high": 529.86, "jitter": 37.587}, "bandwidth": 113750000}, "download": {"bytes": 230789788, "elapsed": 14301, "latency": {"iqm": 104.125, "low": 23.72, "high": 269.563, "jitter": 13.447}, "bandwidth": 115625000}, "interface": {"name": "eth0", "isVpn": false, "macAddr": "00:00:00:00:00:00", "externalIp": "203.0.113.1", "internalIp": "127.0.0.1"}, "timestamp": "2024-03-01T01:00:00Z", "packetLoss": 0.5}', true);

    $dto = OoklaResult::fromArray($payload);

    expect($dto->ping)->toBe(19.133)
        ->and($dto->pingJitter)->toBe(1.878)
        ->and($dto->pingLow)->toBe(17.841)
        ->and($dto->pingHigh)->toBe(24.077)
        ->and($dto->download)->toBe(115625000)
        ->and($dto->downloadBytes)->toBe(230789788)
        ->and($dto->downloadJitter)->toBe(13.447)
        ->and($dto->downloadLatencyIqm)->toBe(104.125)
        ->and($dto->downloadLatencyLow)->toBe(23.72)
        ->and($dto->downloadLatencyHigh)->toBe(269.563)
        ->and($dto->downloadElapsed)->toBe(14301)
        ->and($dto->upload)->toBe(113750000)
        ->and($dto->uploadBytes)->toBe(124297377)
        ->and($dto->uploadJitter)->toBe(37.587)
        ->and($dto->uploadLatencyIqm)->toBe(341.111)
        ->and($dto->uploadLatencyLow)->toBe(16.663)
        ->and($dto->uploadLatencyHigh)->toBe(529.86)
        ->and($dto->uploadElapsed)->toBe(9628)
        ->and($dto->packetLoss)->toBe(0.5)
        ->and($dto->isp)->toBe('Speedtest Communications')
        ->and($dto->ipAddress)->toBe('203.0.113.1')
        ->and($dto->serverId)->toBe(12)
        ->and($dto->serverName)->toBe('Speedtest')
        ->and($dto->serverHost)->toBe('docs.speedtest-tracker.dev')
        ->and($dto->serverIp)->toBe('127.0.0.1')
        ->and($dto->serverCountry)->toBe('United States')
        ->and($dto->serverLocation)->toBe('New York City, NY')
        ->and($dto->resultUrl)->toBe('https://docs.speedtest-tracker.dev');
});

it('converts the dto into snake_case model attributes', function () {
    $dto = OoklaResult::fromArray([
        'ping' => ['latency' => 19.1, 'jitter' => 1.2, 'low' => 17.1, 'high' => 24.1],
        'download' => ['bandwidth' => 100, 'bytes' => 200, 'elapsed' => 300, 'latency' => ['jitter' => 1, 'iqm' => 2, 'low' => 3, 'high' => 4]],
        'upload' => ['bandwidth' => 10, 'bytes' => 20, 'elapsed' => 30, 'latency' => ['jitter' => 5, 'iqm' => 6, 'low' => 7, 'high' => 8]],
        'packetLoss' => 0,
        'isp' => 'Speedtest Communications',
        'interface' => ['externalIp' => '203.0.113.1'],
        'server' => ['id' => 1, 'name' => 'Speedtest', 'host' => 'host', 'ip' => '127.0.0.1', 'country' => 'US', 'location' => 'NYC'],
        'result' => ['url' => 'https://example.test'],
    ]);

    expect($dto->toModelAttributes())->toBe([
        'ping' => 19.1,
        'ping_jitter' => 1.2,
        'ping_low' => 17.1,
        'ping_high' => 24.1,
        'download' => 100,
        'download_bytes' => 200,
        'download_jitter' => 1.0,
        'download_latency_iqm' => 2.0,
        'download_latency_low' => 3.0,
        'download_latency_high' => 4.0,
        'download_elapsed' => 300,
        'upload' => 10,
        'upload_bytes' => 20,
        'upload_jitter' => 5.0,
        'upload_latency_iqm' => 6.0,
        'upload_latency_low' => 7.0,
        'upload_latency_high' => 8.0,
        'upload_elapsed' => 30,
        'packet_loss' => 0.0,
        'isp' => 'Speedtest Communications',
        'ip_address' => '203.0.113.1',
        'server_id' => 1,
        'server_name' => 'Speedtest',
        'server_host' => 'host',
        'server_ip' => '127.0.0.1',
        'server_country' => 'US',
        'server_location' => 'NYC',
        'result_url' => 'https://example.test',
    ]);
});

it('leaves properties null when keys are missing from the payload', function () {
    $dto = OoklaResult::fromArray(['type' => 'log', 'message' => 'boom']);

    expect($dto->ping)->toBeNull()
        ->and($dto->download)->toBeNull()
        ->and($dto->upload)->toBeNull()
        ->and($dto->isp)->toBeNull()
        ->and($dto->serverId)->toBeNull();
});
