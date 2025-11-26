<?php

use App\Enums\ResultStatus;
use App\Models\Result;

it('returns download statistics', function () {
    Result::factory()->count(5)->create([
        'status' => ResultStatus::Completed,
        'download' => 100000000, // 100 Mbps
    ]);

    Result::factory()->create([
        'status' => ResultStatus::Completed,
        'download' => 200000000, // 200 Mbps (highest)
    ]);

    Result::factory()->create([
        'status' => ResultStatus::Completed,
        'download' => 50000000, // 50 Mbps (lowest)
    ]);

    $response = $this->getJson('/api/public/statistics/download');

    $response->assertOk();
    $response->assertJsonStructure(['metric', 'latest', 'average', 'lowest', 'highest']);
    $response->assertJson([
        'metric' => 'download',
        'lowest' => 50000000.0,
        'highest' => 200000000.0,
    ]);
});

it('returns upload statistics', function () {
    Result::factory()->count(3)->create([
        'status' => ResultStatus::Completed,
        'upload' => 50000000, // 50 Mbps
    ]);

    $response = $this->getJson('/api/public/statistics/upload');

    $response->assertOk();
    $response->assertJson([
        'metric' => 'upload',
    ]);
});

it('returns ping statistics', function () {
    Result::factory()->count(3)->create([
        'status' => ResultStatus::Completed,
        'ping' => 25.5,
    ]);

    $response = $this->getJson('/api/public/statistics/ping');

    $response->assertOk();
    $response->assertJson([
        'metric' => 'ping',
    ]);
});

it('rejects invalid metric', function () {
    $response = $this->getJson('/api/public/statistics/invalid');

    $response->assertBadRequest();
    $response->assertJsonStructure(['error']);
});

it('filters statistics by time range', function () {
    // Old result (outside 24h)
    Result::factory()->create([
        'status' => ResultStatus::Completed,
        'download' => 50000000,
        'created_at' => now()->subDays(2),
    ]);

    // Recent results (within 24h)
    Result::factory()->create([
        'status' => ResultStatus::Completed,
        'download' => 100000000,
        'created_at' => now()->subHours(12),
    ]);

    Result::factory()->create([
        'status' => ResultStatus::Completed,
        'download' => 150000000,
        'created_at' => now()->subHours(6),
    ]);

    $response = $this->getJson('/api/public/statistics/download?time_range=24h');

    $response->assertOk();
    $response->assertJson([
        'lowest' => 100000000.0, // Old result excluded
        'highest' => 150000000.0,
    ]);
});

it('filters statistics by server', function () {
    Result::factory()->create([
        'status' => ResultStatus::Completed,
        'server_id' => 123,
        'download' => 100000000,
    ]);

    Result::factory()->create([
        'status' => ResultStatus::Completed,
        'server_id' => 456,
        'download' => 200000000,
    ]);

    $response = $this->getJson('/api/public/statistics/download?server=123');

    $response->assertOk();
    $response->assertJson([
        'highest' => 100000000.0, // Only server 123 included
        'lowest' => 100000000.0,
    ]);
});
