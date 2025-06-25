<?php

use App\Enums\ResultStatus;
use App\Models\Result;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Sanctum\Sanctum;

describe('V1 API - Stats', function () {
    test('returns aggregated statistics', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['results:read']);

        // Create test results with known values
        Result::factory()->create([
            'ping' => 10,
            'download' => 100,
            'upload' => 50,
            'status' => ResultStatus::Completed,
        ]);

        Result::factory()->create([
            'ping' => 20,
            'download' => 200,
            'upload' => 100,
            'status' => ResultStatus::Completed,
        ]);

        $response = $this->get('/api/v1/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_results',
                    'ping' => ['avg', 'min', 'max'],
                    'download' => ['avg', 'min', 'max'],
                    'upload' => ['avg', 'min', 'max'],
                ],
            ]);

        $data = $response->json('data');
        expect($data['total_results'])->toBe(2);
        expect($data['ping']['avg'])->toBe(15);
        expect($data['ping']['min'])->toBe(10);
        expect($data['ping']['max'])->toBe(20);
        expect($data['download']['avg'])->toBe(150);
        expect($data['download']['min'])->toBe(100);
        expect($data['download']['max'])->toBe(200);
        expect($data['upload']['avg'])->toBe(75);
        expect($data['upload']['min'])->toBe(50);
        expect($data['upload']['max'])->toBe(100);
    });

    test('requires authentication', function () {
        $response = $this->get('/api/v1/stats');

        $response->assertStatus(302); // Redirects to login page
    });

    test('requires results:read permission', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, []); // No permissions

        $response = $this->get('/api/v1/stats');

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You do not have permission to view statistics.',
            ]);
    });

    test('returns zero values when no results exist', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['results:read']);

        $response = $this->get('/api/v1/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_results',
                    'ping' => ['avg', 'min', 'max'],
                    'download' => ['avg', 'min', 'max'],
                    'upload' => ['avg', 'min', 'max'],
                ],
            ]);

        $data = $response->json('data');
        expect($data['total_results'])->toBe(0);
        expect($data['ping']['avg'])->toBe(0);
        expect($data['ping']['min'])->toBe(0);
        expect($data['ping']['max'])->toBe(0);
        expect($data['download']['avg'])->toBe(0);
        expect($data['download']['min'])->toBe(0);
        expect($data['download']['max'])->toBe(0);
        expect($data['upload']['avg'])->toBe(0);
        expect($data['upload']['min'])->toBe(0);
        expect($data['upload']['max'])->toBe(0);
    });

    test('filters by start date', function () {
        $now = Carbon::parse('2023-01-10 12:00:00');
        Carbon::setTestNow($now);
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['results:read']);

        // Create results with different dates
        Result::factory()->create([
            'ping' => 10,
            'download' => 100,
            'upload' => 50,
            'created_at' => $now->copy()->subDays(2), // 2023-01-08 12:00:00
        ]);
        Result::factory()->create([
            'ping' => 20,
            'download' => 200,
            'upload' => 100,
            'created_at' => $now->copy()->subDays(1), // 2023-01-09 12:00:00
        ]);

        $response = $this->get('/api/v1/stats?filter[start_at]=>='.$now->copy()->subDays(1)->toDateTimeString());

        $response->assertStatus(200);

        $data = $response->json('data');
        expect($data['total_results'])->toBe(1);
        expect($data['ping']['avg'])->toBe(20);
        expect($data['download']['avg'])->toBe(200);
        expect($data['upload']['avg'])->toBe(100);
        Carbon::setTestNow();
    });

    test('filters by end date', function () {
        $now = Carbon::parse('2023-01-10 12:00:00');
        Carbon::setTestNow($now);
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['results:read']);

        // Create results with different dates
        Result::factory()->create([
            'ping' => 10,
            'download' => 100,
            'upload' => 50,
            'created_at' => $now->copy()->subDays(2), // 2023-01-08 12:00:00
        ]);
        Result::factory()->create([
            'ping' => 20,
            'download' => 200,
            'upload' => 100,
            'created_at' => $now->copy()->subDays(1), // 2023-01-09 12:00:00
        ]);

        $response = $this->get('/api/v1/stats?filter[end_at]=<='.$now->copy()->subDays(2)->toDateTimeString());

        $response->assertStatus(200);

        $data = $response->json('data');
        expect($data['total_results'])->toBe(1);
        expect($data['ping']['avg'])->toBe(10);
        expect($data['download']['avg'])->toBe(100);
        expect($data['upload']['avg'])->toBe(50);
        Carbon::setTestNow();
    });

    test('filters by date range', function () {
        $now = Carbon::parse('2023-01-10 12:00:00');
        Carbon::setTestNow($now);
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['results:read']);

        // Create results with different dates
        Result::factory()->create([
            'ping' => 10,
            'download' => 100,
            'upload' => 50,
            'created_at' => $now->copy()->subDays(3), // 2023-01-07 12:00:00
        ]);
        Result::factory()->create([
            'ping' => 20,
            'download' => 200,
            'upload' => 100,
            'created_at' => $now->copy()->subDays(1), // 2023-01-09 12:00:00
        ]);
        Result::factory()->create([
            'ping' => 30,
            'download' => 300,
            'upload' => 150,
            'created_at' => $now, // 2023-01-10 12:00:00
        ]);

        $response = $this->get('/api/v1/stats?filter[start_at]=>='.$now->copy()->subDays(1)->toDateTimeString().'&filter[end_at]=<='.$now->toDateTimeString());

        $response->assertStatus(200);

        $data = $response->json('data');
        expect($data['total_results'])->toBe(2);
        expect($data['ping']['avg'])->toBe(25);
        expect($data['download']['avg'])->toBe(250);
        expect($data['upload']['avg'])->toBe(125);
        Carbon::setTestNow();
    });

    test('handles null values in calculations', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['results:read']);

        Result::factory()->create([
            'ping' => null,
            'download' => null,
            'upload' => null,
            'status' => ResultStatus::Failed,
        ]);

        Result::factory()->create([
            'ping' => 10,
            'download' => 100,
            'upload' => 50,
            'status' => ResultStatus::Completed,
        ]);

        $response = $this->get('/api/v1/stats');

        $response->assertStatus(200);

        $data = $response->json('data');
        expect($data['total_results'])->toBe(2);
        // Should only calculate averages for non-null values
        expect($data['ping']['avg'])->toBe(10);
        expect($data['download']['avg'])->toBe(100);
        expect($data['upload']['avg'])->toBe(50);
    });

    test('includes filters in response', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['results:read']);
        Result::factory()->create();

        $response = $this->get('/api/v1/stats?filter[start_at][gte]='.now()->subDays(1)->toISOString());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
            ]);
    });
});
