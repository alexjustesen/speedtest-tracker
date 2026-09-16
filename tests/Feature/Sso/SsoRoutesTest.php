<?php

use Illuminate\Support\Facades\Cache;

function enableSso(array $overrides = []): void
{
    config(['services.openidconnect' => array_merge((array) config('services.openidconnect'), [
        'enabled' => true,
        'base_url' => 'https://id.test',
        'client_id' => 'my-client',
        'client_secret' => 'my-secret',
    ], $overrides)]);
}

function fakeDiscovery(string $baseUrl = 'https://id.test'): void
{
    Cache::put('openidconnect_discovery_'.md5($baseUrl.'/.well-known/openid-configuration'), [
        'issuer' => $baseUrl,
        'authorization_endpoint' => $baseUrl.'/application/o/authorize/',
        'token_endpoint' => $baseUrl.'/application/o/token/',
        'userinfo_endpoint' => $baseUrl.'/application/o/userinfo/',
        'jwks_uri' => $baseUrl.'/application/o/jwks/',
    ], 3600);
}

it('returns 404 for the sso routes when disabled', function () {
    test()->get(route('sso.redirect'))->assertNotFound();
    test()->get(route('sso.callback'))->assertNotFound();
});

it('returns 404 when enabled without client credentials', function () {
    enableSso(['client_id' => null, 'client_secret' => null]);

    test()->get(route('sso.redirect'))->assertNotFound();
});

it('redirects to the discovered authorize url when enabled', function () {
    enableSso();
    fakeDiscovery();

    $response = test()->get(route('sso.redirect'));

    $response->assertRedirect();

    $location = $response->headers->get('Location');

    expect($location)
        ->toContain('https://id.test/application/o/authorize/')
        ->toContain('code_challenge=')
        ->toContain('state=');

    parse_str((string) parse_url((string) $location, PHP_URL_QUERY), $query);

    expect($query)
        ->toMatchArray([
            'client_id' => 'my-client',
            'redirect_uri' => route('sso.callback'),
            'response_type' => 'code',
            'scope' => 'openid email profile',
        ]);
});

it('sends only the configured scopes', function () {
    enableSso(['scopes' => ['openid', 'email']]);
    fakeDiscovery();

    $location = (string) test()->get(route('sso.redirect'))->headers->get('Location');

    parse_str((string) parse_url($location, PHP_URL_QUERY), $query);

    expect($query['scope'])->toBe('openid email');
});

it('shows the sso button on the login page when enabled', function () {
    enableSso();

    test()->get(route('filament.admin.auth.login'))
        ->assertOk()
        ->assertSee('Sign in with SSO')
        ->assertSee('auth/sso/redirect');
});

it('shows a custom button label when configured', function () {
    enableSso(['button_label' => 'Continue with Authentik']);

    test()->get(route('filament.admin.auth.login'))
        ->assertOk()
        ->assertSee('Continue with Authentik');
});

it('hides the sso button on the login page when disabled', function () {
    test()->get(route('filament.admin.auth.login'))
        ->assertOk()
        ->assertDontSee('auth/sso/redirect');
});
