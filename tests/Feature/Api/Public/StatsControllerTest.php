<?php

use App\Enums\ResultStatus;
use App\Models\Result;

it('returns latest stats', function () {
    $result = Result::factory()->create([
        'status' => ResultStatus::Completed,
        'ping' => 25.5,
        'download' => 100000000,
        'upload' => 50000000,
    ]);

    $response = $this->getJson('/api/public/stats');

    $response->assertOk();
    $response->assertJsonStructure(['id', 'ping', 'download', 'upload', 'server_id', 'server_name', 'created_at']);
    $response->assertJson([
        'id' => $result->id,
        'ping' => '25.5',
    ]);
});

it('filters stats by time range', function () {
    // Create old result (outside 24h)
    Result::factory()->create([
        'status' => ResultStatus::Completed,
        'created_at' => now()->subDays(2),
    ]);

    // Create recent result (within 24h)
    $recentResult = Result::factory()->create([
        'status' => ResultStatus::Completed,
        'created_at' => now()->subHours(12),
    ]);

    $response = $this->getJson('/api/public/stats?time_range=24h');

    $response->assertOk();
    $response->assertJson([
        'id' => $recentResult->id,
    ]);
});

it('filters stats by server', function () {
    $result1 = Result::factory()->create([
        'status' => ResultStatus::Completed,
        'server_id' => 123,
        'server_name' => 'Server A',
    ]);

    $result2 = Result::factory()->create([
        'status' => ResultStatus::Completed,
        'server_id' => 456,
        'server_name' => 'Server B',
    ]);

    $response = $this->getJson('/api/public/stats?server=123');

    $response->assertOk();
    $response->assertJson([
        'id' => $result1->id,
        'server_id' => 123,
    ]);
});
