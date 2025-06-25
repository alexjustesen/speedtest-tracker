<?php

use App\Actions\Ookla\RunSpeedtest as RunSpeedtestAction;
use App\Enums\ResultService;
use App\Enums\ResultStatus;
use App\Models\Result;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

// Helper to mock both run and handle
function mockRunSpeedtestAction($serverId = null, $result = null, $throw = null)
{
    test()->mock(RunSpeedtestAction::class, function ($mock) use ($serverId, $result, $throw) {
        if ($throw) {
            $mock->shouldReceive('run')->with($serverId)->andThrow($throw);
            $mock->shouldReceive('handle')->andThrow($throw);
        } else {
            // Ensure the result has timestamps set
            if ($result && ! $result->created_at) {
                $result->created_at = now();
            }
            if ($result && ! $result->updated_at) {
                $result->updated_at = now();
            }

            $mock->shouldReceive('run')->with($serverId)->andReturn($result);
            $mock->shouldReceive('handle')->andReturn($result);
        }
    });
}

describe('V1 API - Run Speedtest', function () {
    test('creates and queues speedtest when user has permission', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['speedtests:run']);
        $result = Result::factory()->make([
            'id' => 1,
            'service' => ResultService::Ookla,
            'status' => ResultStatus::Waiting,
            'scheduled' => false,
        ]);
        mockRunSpeedtestAction(null, $result);
        $response = $this->post('/api/v1/speedtests/run');
        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Speedtest added to the queue.',
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'service',
                    'status',
                    'scheduled',
                    'created_at',
                    'updated_at',
                ],
            ]);
    });

    test('creates speedtest with specific server when provided', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['speedtests:run']);
        $serverId = 12345;
        $result = Result::factory()->make([
            'id' => 1,
            'service' => ResultService::Ookla,
            'status' => ResultStatus::Waiting,
            'scheduled' => false,
        ]);
        mockRunSpeedtestAction($serverId, $result);
        $response = $this->post('/api/v1/speedtests/run?server_id='.$serverId);
        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Speedtest added to the queue.',
            ]);
    });

    test('requires authentication', function () {
        $response = $this->post('/api/v1/speedtests/run');
        $response->assertStatus(302); // Redirects to login page
    });

    test('returns 403 when user lacks permission', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, []); // No permissions
        $response = $this->post('/api/v1/speedtests/run');
        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You do not have permission to run speedtests.',
            ]);
    });

    test('validates server_id parameter', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['speedtests:run']);
        $response = $this->post('/api/v1/speedtests/run?server_id=invalid');
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Validation failed.',
            ])
            ->assertJsonStructure([
                'data' => [
                    'server_id',
                ],
            ]);
    });

    test('accepts valid server_id parameter', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['speedtests:run']);
        $result = Result::factory()->make([
            'id' => 1,
            'service' => ResultService::Ookla,
            'status' => ResultStatus::Waiting,
            'scheduled' => false,
        ]);
        mockRunSpeedtestAction(12345, $result);
        $response = $this->post('/api/v1/speedtests/run?server_id=12345');
        $response->assertStatus(201);
    });

    test('handles action exceptions gracefully', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['speedtests:run']);
        mockRunSpeedtestAction(null, null, new \Exception('Speedtest failed'));
        $response = $this->post('/api/v1/speedtests/run');
        $response->assertStatus(500);
    });

    test('validates server_id as integer', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['speedtests:run']);
        $response = $this->post('/api/v1/speedtests/run?server_id=abc');
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Validation failed.',
            ]);
        $response = $this->post('/api/v1/speedtests/run?server_id=12.34');
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Validation failed.',
            ]);
    });

    test('accepts server_id as string integer', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['speedtests:run']);
        $result = Result::factory()->make([
            'id' => 1,
            'service' => ResultService::Ookla,
            'status' => ResultStatus::Waiting,
            'scheduled' => false,
        ]);
        mockRunSpeedtestAction(12345, $result);
        $response = $this->post('/api/v1/speedtests/run?server_id=12345');
        $response->assertStatus(201);
    });
});
