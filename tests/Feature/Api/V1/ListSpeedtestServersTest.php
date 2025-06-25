<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;

describe('V1 API - List Speedtest Servers', function () {
    test('returns servers when user has permission', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['ookla:list-servers']);

        // Mock the HTTP client to return test data
        Http::fake([
            'https://www.speedtest.net/api/js/servers*' => Http::response([
                [
                    'id' => 12345,
                    'host' => 'speedtest.example.com',
                    'sponsor' => 'Example ISP',
                    'name' => 'Example City',
                    'country' => 'US',
                ],
                [
                    'id' => 67890,
                    'host' => 'speedtest2.example.com',
                    'sponsor' => 'Another ISP',
                    'name' => 'Another City',
                    'country' => 'CA',
                ],
            ], 200),
        ]);

        $response = $this->get('/api/v1/ookla/list-servers');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'id' => 12345,
                        'host' => 'speedtest.example.com',
                        'name' => 'Example ISP',
                        'location' => 'Example City',
                        'country' => 'US',
                    ],
                    [
                        'id' => 67890,
                        'host' => 'speedtest2.example.com',
                        'name' => 'Another ISP',
                        'location' => 'Another City',
                        'country' => 'CA',
                    ],
                ],
                'message' => 'Speedtest servers fetched successfully.',
            ]);
    });

    test('requires authentication', function () {
        $response = $this->get('/api/v1/ookla/list-servers');

        $response->assertStatus(302); // Redirects to login page
    });

    test('returns 403 when user lacks permission', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, []); // No permissions

        $response = $this->get('/api/v1/ookla/list-servers');

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You do not have permission to view speedtest servers.',
            ]);
    });

    test('returns only message when no servers available', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['ookla:list-servers']);

        // Mock the HTTP client to return empty array
        Http::fake([
            'https://www.speedtest.net/api/js/servers*' => Http::response([], 200),
        ]);

        $response = $this->get('/api/v1/ookla/list-servers');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Speedtest servers fetched successfully.',
            ])
            ->assertJsonMissing(['data']); // data field should be filtered out when empty
    });

    test('handles API errors gracefully', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['ookla:list-servers']);

        // Mock the HTTP client to simulate a network error
        Http::fake([
            'https://www.speedtest.net/api/js/servers*' => Http::response('', 500),
        ]);

        $response = $this->get('/api/v1/ookla/list-servers');

        // Should return 200 with only the message field, no data field
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Speedtest servers fetched successfully.',
            ])
            ->assertJsonMissing(['data']);
    });
});
