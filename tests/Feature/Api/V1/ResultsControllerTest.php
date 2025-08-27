<?php

use App\Models\Result;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user, ['results:read']);
});

test('it can filter results by date range using start_at and end_at', function () {
    // Create results on different dates
    $result1 = Result::factory()->create([
        'created_at' => Carbon::now()->subDays(10),
    ]);

    $result2 = Result::factory()->create([
        'created_at' => Carbon::now()->subDays(5),
    ]);

    $result3 = Result::factory()->create([
        'created_at' => Carbon::now()->subDays(1),
    ]);

    // Filter by date range
    $startDate = Carbon::now()->subDays(7)->format('Y-m-d');
    $endDate = Carbon::now()->format('Y-m-d');

    $response = $this->getJson("/api/v1/results?filter[start_at]>={$startDate}&filter[end_at]<={$endDate}");

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data'); // Should return result2 and result3
});

test('it validates date filter parameters', function () {
    $response = $this->getJson('/api/v1/results?filter[start_at]=>invalid-date');

    $response->assertStatus(422)
        ->assertJson([
            'data' => [
                'filter.start_at' => ['The filter.start at field must be a valid date.'],
            ],
            'message' => 'Validation failed.',
        ]);
});

test('it validates end_at is after or equal to start_at', function () {
    $startDate = Carbon::now()->format('Y-m-d');
    $endDate = Carbon::now()->subDays(1)->format('Y-m-d');

    $response = $this->getJson("/api/v1/results?filter[start_at]>={$startDate}&filter[end_at]<={$endDate}");

    $response->assertStatus(422)
        ->assertJson([
            'data' => [
                'filter.end_at' => ['The filter.end at field must be a date after or equal to filter.start at.'],
            ],
            'message' => 'Validation failed.',
        ]);
});

test('it returns forbidden when user lacks permission', function () {
    // Create a user without results:read permission
    $unauthorizedUser = User::factory()->create();
    Sanctum::actingAs($unauthorizedUser, []);

    $response = $this->getJson('/api/v1/results');

    $response->assertStatus(403)
        ->assertJson([
            'message' => 'You do not have permission to view results.',
        ]);
});
