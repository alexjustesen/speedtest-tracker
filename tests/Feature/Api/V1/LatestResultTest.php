<?php

use App\Enums\ResultStatus;
use App\Models\Result;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

describe('V1 API - Latest Result', function () {
    test('returns 404 when no results exist', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['results:read']);

        $response = $this->get('/api/v1/results/latest');

        $response->assertStatus(404);
    });

    test('returns latest result regardless of status', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['results:read']);

        // Create some test results
        $oldResult = Result::factory()->create([
            'created_at' => now()->subDays(2),
        ]);

        $latestResult = Result::factory()->create([
            'created_at' => now()->subDay(),
        ]);

        $response = $this->get('/api/v1/results/latest');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'ping',
                    'download',
                    'upload',
                    'data',
                    'scheduled',
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $responseData = $response->json('data');
        expect($responseData['id'])->toBe($latestResult->id);
    });

    test('requires authentication', function () {
        $response = $this->get('/api/v1/results/latest');

        $response->assertStatus(302); // Redirects to login page
    });

    test('requires results:read permission', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, []); // No permissions

        $response = $this->get('/api/v1/results/latest');

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You do not have permission to view results.',
            ]);
    });

    test('returns result with correct structure', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['results:read']);

        $result = Result::factory()->create([
            'ping' => 15,
            'download' => 150,
            'upload' => 75,
            'data' => [
                'server' => [
                    'id' => 12345,
                    'host' => 'speedtest.example.com',
                    'name' => 'Example Server',
                ],
                'result' => [
                    'url' => 'https://example.com/result',
                ],
            ],
            'scheduled' => true,
            'status' => ResultStatus::Completed,
        ]);

        $response = $this->get('/api/v1/results/latest');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $result->id,
                    'ping' => $result->ping,
                    'download' => $result->download,
                    'upload' => $result->upload,
                    'scheduled' => $result->scheduled,
                    'status' => $result->status->value,
                ],
            ]);
    });
});
