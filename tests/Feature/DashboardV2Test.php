<?php

it('loads dashboard v2 when enabled', function () {
    config(['speedtest.dashboard_v2.enabled' => true]);

    $response = $this->get('/v2');

    $response->assertOk();
    $response->assertViewIs('dashboard-v2');
});

it('redirects to home when disabled', function () {
    config(['speedtest.dashboard_v2.enabled' => false]);

    $response = $this->get('/v2');

    $response->assertRedirect(route('home'));
});
