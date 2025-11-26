<?php

use App\Enums\ResultStatus;
use App\Models\Result;

it('returns download chart data', function () {
    $result1 = Result::factory()->create([
        'status' => ResultStatus::Completed,
        'download' => 100000000,
        'created_at' => now()->subHours(2),
    ]);

    $result2 = Result::factory()->create([
        'status' => ResultStatus::Completed,
        'download' => 150000000,
        'created_at' => now()->subHours(1),
    ]);

    $response = $this->getJson('/api/public/chart-data/download');

    $response->assertOk();
    $response->assertJsonStructure(['metric', 'data', 'average']);
    $response->assertJson([
        'metric' => 'download',
        'average' => 125000000.0,
    ]);

    // Verify data array structure
    expect($response->json('data'))->toHaveCount(2);
    expect($response->json('data.0'))->toHaveKeys(['x', 'y']);
    expect($response->json('data.0.y'))->toBe(100000000.0);
    expect($response->json('data.1.y'))->toBe(150000000.0);
});

it('returns upload chart data', function () {
    Result::factory()->count(3)->create([
        'status' => ResultStatus::Completed,
        'upload' => 50000000,
    ]);

    $response = $this->getJson('/api/public/chart-data/upload');

    $response->assertOk();
    $response->assertJson([
        'metric' => 'upload',
    ]);
});

it('returns ping chart data', function () {
    Result::factory()->count(3)->create([
        'status' => ResultStatus::Completed,
        'ping' => 25.5,
    ]);

    $response = $this->getJson('/api/public/chart-data/ping');

    $response->assertOk();
    $response->assertJson([
        'metric' => 'ping',
    ]);
});

it('rejects invalid metric', function () {
    $response = $this->getJson('/api/public/chart-data/invalid');

    $response->assertBadRequest();
    $response->assertJsonStructure(['error']);
});

it('orders chart data by created_at', function () {
    $result3 = Result::factory()->create([
        'status' => ResultStatus::Completed,
        'download' => 300000000,
        'created_at' => now()->subHours(1),
    ]);

    $result1 = Result::factory()->create([
        'status' => ResultStatus::Completed,
        'download' => 100000000,
        'created_at' => now()->subHours(3),
    ]);

    $result2 = Result::factory()->create([
        'status' => ResultStatus::Completed,
        'download' => 200000000,
        'created_at' => now()->subHours(2),
    ]);

    $response = $this->getJson('/api/public/chart-data/download');

    $response->assertOk();

    // Verify data is ordered chronologically
    expect($response->json('data.0.y'))->toBe(100000000.0);
    expect($response->json('data.1.y'))->toBe(200000000.0);
    expect($response->json('data.2.y'))->toBe(300000000.0);
});

it('filters chart data by time range', function () {
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

    $response = $this->getJson('/api/public/chart-data/download?time_range=24h');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1); // Old result excluded
    expect($response->json('data.0.y'))->toBe(100000000.0);
});

it('filters chart data by server', function () {
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

    $response = $this->getJson('/api/public/chart-data/download?server=123');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1); // Only server 123 included
    expect($response->json('data.0.y'))->toBe(100000000.0);
});
