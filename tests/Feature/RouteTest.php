<?php

use App\Models\Result;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

describe('auth', function () {
    test('"login" redirects to Filament login page', function () {
        $response = $this->get('/login');

        $response->assertRedirect('/admin/login')
            ->assertStatus(302);
    });
});

describe('page', function () {
    test('can render "home" route', function () {
        config()->set('speedtest.public_dashboard', true);

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

    test('can render "admin" route when authenticated', function () {
        $this->actingAs(User::factory()->create());

        $response = $this->get('/admin');

        $response->assertStatus(200);
    });
});

describe('redirects', function () {
    test('redirect "admin" to login page when not authenticated', function () {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login')
            ->assertStatus(302);
    });

    test('redirect "home" to login page when public dashboard is disabled', function () {
        config()->set('speedtest.public_dashboard', false);

        Result::factory()->create();

        $response = $this->get('/');

        $response->assertRedirect('/admin/login')
            ->assertStatus(302);
    });

    test('redirect "home" route to "getting-started" when there are no results', function () {
        $response = $this->get('/');

        $response->assertRedirect('/getting-started')
            ->assertStatus(302);
    });
});
