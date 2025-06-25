<?php

use App\Enums\ResultStatus;
use App\Models\Result;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

describe('V1 API - Show Result', function () {
    test('returns 404 when result does not exist', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['results:read']);

        $response = $this->get('/api/v1/results/999');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Result not found.',
            ]);
    });

    test('returns specific result by id', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['results:read']);

        $result = Result::factory()->create([
            'ping' => 20,
            'download' => 200,
            'upload' => 100,
            'data' => [
                'server' => [
                    'id' => 54321,
                    'host' => 'speedtest.example.org',
                    'name' => 'Test Server',
                ],
                'result' => [
                    'url' => 'https://example.org/result',
                ],
            ],
            'scheduled' => false,
            'status' => ResultStatus::Completed,
        ]);

        $response = $this->get("/api/v1/results/{$result->id}");

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

    test('requires authentication', function () {
        $result = Result::factory()->create();

        $response = $this->get("/api/v1/results/{$result->id}");

        $response->assertStatus(302); // Redirects to login page
    });

    test('requires results:read permission', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, []); // No permissions
        $result = Result::factory()->create();

        $response = $this->get("/api/v1/results/{$result->id}");

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You do not have permission to view results.',
            ]);
    });

    test('returns result with correct structure', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['results:read']);
        $result = Result::factory()->create();

        $response = $this->get("/api/v1/results/{$result->id}");

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
    });

    test('handles failed results', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['results:read']);

        $result = Result::factory()->create([
            'status' => ResultStatus::Failed,
            'ping' => null,
            'download' => null,
            'upload' => null,
        ]);

        $response = $this->get("/api/v1/results/{$result->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $result->id,
                    'status' => ResultStatus::Failed->value,
                ],
            ]);
    });
});
