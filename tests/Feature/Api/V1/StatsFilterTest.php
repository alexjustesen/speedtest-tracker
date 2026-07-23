<?php

use App\Models\Result;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Sanctum::actingAs(User::factory()->create(), ['results:read']);
});

test('stats aggregate only counts results within the start/end range', function () {
    Result::factory()->create(['created_at' => '2025-12-31 12:00:00']);
    Result::factory()->create(['created_at' => '2026-01-01 09:00:00']);
    Result::factory()->create(['created_at' => '2026-01-31 15:00:00']);
    Result::factory()->create(['created_at' => '2026-02-01 08:00:00']);

    $response = $this->getJson('/api/v1/stats?filter[start_at]=2026-01-01&filter[end_at]=2026-01-31');

    $response->assertOk();

    expect($response->json('data.total_results'))->toBe(2);
});

test('stats endpoint rejects an invalid date filter with a 422', function () {
    $response = $this->getJson('/api/v1/stats?filter[end_at]=not-a-date');

    $response->assertStatus(422);
});
