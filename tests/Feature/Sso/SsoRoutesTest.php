<?php

use App\Settings\SsoSettings;

function enableSso(): void
{
    $settings = app(SsoSettings::class);
    $settings->enabled = true;
    $settings->provider = 'authentik';
    $settings->base_url = 'https://authentik.test';
    $settings->client_id = 'my-client';
    $settings->client_secret = 'my-secret';
    $settings->save();
}

it('returns 404 for the sso routes when disabled', function () {
    test()->get(route('sso.redirect', 'authentik'))->assertNotFound();
    test()->get(route('sso.callback', 'authentik'))->assertNotFound();
});

it('returns 404 for an unknown provider', function () {
    enableSso();

    test()->get(route('sso.redirect', 'unknown'))->assertNotFound();
});

it('redirects to the provider authorize url when enabled', function () {
    enableSso();

    $response = test()->get(route('sso.redirect', 'authentik'));

    $response->assertRedirect();

    $location = $response->headers->get('Location');

    expect($location)
        ->toContain('authentik.test/application/o/authorize/')
        ->toContain('client_id=my-client')
        ->toContain('state=');
});

it('shows the sso button on the login page when enabled', function () {
    enableSso();

    test()->get(route('filament.admin.auth.login'))
        ->assertOk()
        ->assertSee('Sign in with Authentik')
        ->assertSee('auth/sso/authentik/redirect');
});

it('hides the sso button on the login page when disabled', function () {
    test()->get(route('filament.admin.auth.login'))
        ->assertOk()
        ->assertDontSee('auth/sso/authentik/redirect');
});
