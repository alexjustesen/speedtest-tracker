<?php

use App\Models\User;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

describe('version endpoint', function () {
    test('returns version information for users with admin:read ability', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token', ['admin:read']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/version');

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'app' => ['name', 'version', 'build_date'],
                    'updates' => ['latest_version', 'update_available'],
                ],
            ])
            ->assertJsonPath('data.app.version', config('speedtest.build_version'))
            ->assertJsonPath('data.app.name', config('app.name'));
    });

    test('denies access for users without admin:read ability', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token', ['results:read']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/version');

        $response->assertForbidden()
            ->assertJsonPath('message', 'You do not have permission to view version information.');
    });

    test('requires authentication', function () {
        $response = $this->getJson('/api/v1/version');

        $response->assertUnauthorized();
    });

    test('includes update information when available', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token', ['admin:read']);

        // Mock the GitHub service to return a known value
        Cache::put('github.latest_version', 'v1.13.8', now()->addHour());

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/version');

        $response->assertSuccessful()
            ->assertJsonPath('data.updates.latest_version', 'v1.13.8')
            ->assertJsonPath('data.updates.update_available', true);
    });

    test('handles unavailable update information gracefully', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token', ['admin:read']);

        // Mock GitHub service to return false (unavailable)
        Cache::put('github.latest_version', false, now()->addHour());

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/version');

        $response->assertSuccessful()
            ->assertJsonPath('data.updates.latest_version', null)
            ->assertJsonPath('data.updates.update_available', false);
    });
});
