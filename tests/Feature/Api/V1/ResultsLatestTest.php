<?php

use App\Enums\ResultStatus;
use App\Models\Result;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Sanctum::actingAs(User::factory()->create(), ['results:read']);
});

test('latest returns the single most recent result regardless of status', function () {
    Result::factory()->create(['status' => ResultStatus::Completed, 'created_at' => '2026-01-01 00:00:00']);
    $mostRecent = Result::factory()->create(['status' => ResultStatus::Failed, 'created_at' => '2026-01-02 00:00:00']);

    $response = $this->getJson('/api/v1/results/latest');

    $response->assertOk();
    expect($response->json('data.id'))->toBe($mostRecent->id);
});

test('filter[status]=completed returns the last known good result', function () {
    $lastGood = Result::factory()->create(['status' => ResultStatus::Completed, 'created_at' => '2026-01-01 00:00:00']);
    Result::factory()->create(['status' => ResultStatus::Failed, 'created_at' => '2026-01-02 00:00:00']);

    $response = $this->getJson('/api/v1/results/latest?filter[status]=completed');

    $response->assertOk();
    expect($response->json('data.id'))->toBe($lastGood->id);
});

test('filter[status]=completed returns a 404 when there is no completed result', function () {
    Result::factory()->create(['status' => ResultStatus::Failed]);

    $response = $this->getJson('/api/v1/results/latest?filter[status]=completed');

    $response->assertStatus(404);
});

test('an invalid date filter on latest returns a 422', function () {
    Result::factory()->create();

    $response = $this->getJson('/api/v1/results/latest?filter[start_at]=not-a-date');

    $response->assertStatus(422);
});
