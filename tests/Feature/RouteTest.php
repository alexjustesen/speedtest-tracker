<?php

use App\Models\Result;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

describe('page', function () {
    test('can render "home" route', function () {
        Result::factory()->create();

        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('Dashboard');
    });

    test('can render "getting-started" route', function () {
        $response = $this->get('/getting-started');

        $response->assertStatus(200)
            ->assertSee('Getting Started');
    });

    test('redirect "home" route to "getting-started" when there are no results', function () {
        $response = $this->get('/');

        $response->assertRedirect('/getting-started')
            ->assertStatus(302);
    });

    test('can render "admin" route when authenticated', function () {
        $this->actingAs(User::factory()->create());

        $response = $this->get('/admin');

        $response->assertStatus(200);
    });
});

describe('auth', function () {
    test('"login" redirects to Filament login page', function () {
        $response = $this->get('/login');

        $response->assertRedirect('/admin/login')
            ->assertStatus(302);
    });

    test('redirect "admin" to login page when not authenticated', function () {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login')
            ->assertStatus(302);
    });
});
