<?php

use App\Enums\ResultStatus;
use App\Models\Result;

it('returns health data', function () {
    // Create 8 completed and 2 failed tests
    Result::factory()->count(8)->create([
        'status' => ResultStatus::Completed,
    ]);

    Result::factory()->count(2)->create([
        'status' => ResultStatus::Failed,
    ]);

    $response = $this->getJson('/api/public/health');

    $response->assertOk();
    $response->assertJsonStructure(['percentage', 'status', 'total', 'completed', 'failed']);
    $response->assertJson([
        'percentage' => 80.0, // 8 out of 10 = 80%
        'total' => 10,
        'completed' => 8,
        'failed' => 2,
    ]);
});

it('calculates correct percentage', function () {
    // Create 95 completed and 5 failed tests (95% success rate)
    Result::factory()->count(95)->create([
        'status' => ResultStatus::Completed,
    ]);

    Result::factory()->count(5)->create([
        'status' => ResultStatus::Failed,
    ]);

    $response = $this->getJson('/api/public/health');

    $response->assertOk();
    $response->assertJson([
        'percentage' => 95.0,
        'total' => 100,
        'completed' => 95,
        'failed' => 5,
    ]);
});

it('returns null percentage when no results', function () {
    $response = $this->getJson('/api/public/health');

    $response->assertOk();
    $response->assertJson([
        'percentage' => null,
        'status' => null,
        'total' => 0,
        'completed' => 0,
        'failed' => 0,
    ]);
});

it('filters health by time range', function () {
    // Old results (outside 24h)
    Result::factory()->count(5)->create([
        'status' => ResultStatus::Completed,
        'created_at' => now()->subDays(2),
    ]);

    Result::factory()->create([
        'status' => ResultStatus::Failed,
        'created_at' => now()->subDays(2),
    ]);

    // Recent results (within 24h) - 3 completed, 1 failed
    Result::factory()->count(3)->create([
        'status' => ResultStatus::Completed,
        'created_at' => now()->subHours(12),
    ]);

    Result::factory()->create([
        'status' => ResultStatus::Failed,
        'created_at' => now()->subHours(6),
    ]);

    $response = $this->getJson('/api/public/health?time_range=24h');

    $response->assertOk();
    $response->assertJson([
        'percentage' => 75.0, // 3 out of 4 = 75%
        'total' => 4,
        'completed' => 3,
        'failed' => 1,
    ]);
});

it('filters health by server', function () {
    // Server 123 - 9 completed, 1 failed
    Result::factory()->count(9)->create([
        'status' => ResultStatus::Completed,
        'server_id' => 123,
    ]);

    Result::factory()->create([
        'status' => ResultStatus::Failed,
        'server_id' => 123,
    ]);

    // Server 456 - 5 completed, 5 failed
    Result::factory()->count(5)->create([
        'status' => ResultStatus::Completed,
        'server_id' => 456,
    ]);

    Result::factory()->count(5)->create([
        'status' => ResultStatus::Failed,
        'server_id' => 456,
    ]);

    $response = $this->getJson('/api/public/health?server=123');

    $response->assertOk();
    $response->assertJson([
        'percentage' => 90.0, // 9 out of 10 = 90%
        'total' => 10,
        'completed' => 9,
        'failed' => 1,
    ]);
});

it('returns latest test status as completed', function () {
    Result::factory()->create([
        'status' => ResultStatus::Failed,
        'created_at' => now()->subHours(2),
    ]);

    Result::factory()->create([
        'status' => ResultStatus::Completed,
        'created_at' => now()->subHours(1),
    ]);

    $response = $this->getJson('/api/public/health');

    $response->assertOk();
    $response->assertJson([
        'status' => 'completed',
    ]);
});

it('returns latest test status as failed', function () {
    Result::factory()->create([
        'status' => ResultStatus::Completed,
        'created_at' => now()->subHours(2),
    ]);

    Result::factory()->create([
        'status' => ResultStatus::Failed,
        'created_at' => now()->subHours(1),
    ]);

    $response = $this->getJson('/api/public/health');

    $response->assertOk();
    $response->assertJson([
        'status' => 'failed',
    ]);
});

it('handles 100% success rate', function () {
    Result::factory()->count(10)->create([
        'status' => ResultStatus::Completed,
    ]);

    $response = $this->getJson('/api/public/health');

    $response->assertOk();
    $response->assertJson([
        'percentage' => 100.0,
        'total' => 10,
        'completed' => 10,
        'failed' => 0,
    ]);
});

it('handles 0% success rate', function () {
    Result::factory()->count(10)->create([
        'status' => ResultStatus::Failed,
    ]);

    $response = $this->getJson('/api/public/health');

    $response->assertOk();
    $response->assertJson([
        'percentage' => 0.0,
        'total' => 10,
        'completed' => 0,
        'failed' => 10,
    ]);
});
