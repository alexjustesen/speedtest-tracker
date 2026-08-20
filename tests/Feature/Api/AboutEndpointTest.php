<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

describe('about endpoint', function () {
    test('requires authentication', function () {
        $response = $this->getJson(route('api.v1.about'));

        $response->assertUnauthorized();
    });

    test('returns json 401 instead of redirecting when Accept header is not set', function () {
        $response = $this->get(route('api.v1.about'));

        $response->assertUnauthorized()
            ->assertJson(['message' => 'Unauthenticated.']);
    });

    test('returns json 401 for an invalid bearer token without Accept header', function () {
        $response = $this->withHeaders(['Authorization' => 'Bearer invalid-token'])
            ->get(route('api.v1.about'));

        $response->assertUnauthorized()
            ->assertJson(['message' => 'Unauthenticated.']);
    });

    test('returns application information for an authenticated token', function () {
        config()->set('app.name', 'Speedtest Tracker');
        config()->set('speedtest.build_version', 'v1.14.3');
        config()->set('speedtest.build_date', Carbon\Carbon::parse('2026-05-25'));

        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson(route('api.v1.about'));

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'name' => 'Speedtest Tracker',
                    'build_version' => 'v1.14.3',
                    'build_date' => '2026-05-25',
                ],
                'message' => 'ok',
            ]);
    });
});
