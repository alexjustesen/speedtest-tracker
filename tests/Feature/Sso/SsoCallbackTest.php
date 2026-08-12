<?php

use App\Enums\UserRole;
use App\Models\User;
use App\Settings\SsoSettings;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as SocialiteUser;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    User::query()->delete();
});

function bootSso(array $overrides = []): void
{
    $settings = app(SsoSettings::class);
    $settings->enabled = true;
    $settings->provider = 'authentik';
    $settings->base_url = 'https://authentik.test';
    $settings->client_id = 'my-client';
    $settings->client_secret = 'my-secret';
    $settings->auto_create_users = $overrides['auto_create_users'] ?? true;
    $settings->allow_linking_by_email = $overrides['allow_linking_by_email'] ?? true;
    $settings->default_role = $overrides['default_role'] ?? 'user';
    $settings->role_mapping_enabled = $overrides['role_mapping_enabled'] ?? false;
    $settings->groups_claim = $overrides['groups_claim'] ?? 'groups';
    $settings->admin_groups = $overrides['admin_groups'] ?? [];
    $settings->save();
}

function ssoCallback(): string
{
    return route('sso.callback', ['provider' => 'authentik', 'code' => 'test-code', 'state' => 'test-state']);
}

function fakeAuthentikUser(array $raw): void
{
    $socialiteUser = (new SocialiteUser)
        ->setRaw($raw)
        ->map([
            'id' => $raw['sub'] ?? null,
            'name' => $raw['name'] ?? null,
            'email' => $raw['email'] ?? null,
        ]);

    $provider = Mockery::mock(AbstractProvider::class);
    $provider->shouldReceive('setScopes')->andReturnSelf();
    $provider->shouldReceive('user')->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->andReturn($provider);
}

it('creates and authenticates a new user on a verified email', function () {
    bootSso();

    fakeAuthentikUser([
        'sub' => 'sub-1',
        'email' => 'jane@example.com',
        'email_verified' => true,
        'name' => 'Jane Doe',
    ]);

    test()->get(ssoCallback())->assertRedirect('/');

    test()->assertAuthenticated();

    assertDatabaseHas('users', [
        'email' => 'jane@example.com',
        'name' => 'Jane Doe',
        'sso_provider' => 'authentik',
        'sso_id' => 'sub-1',
        'role' => 'user',
    ]);
});

it('links an existing local account on a verified email', function () {
    bootSso();

    $existing = User::factory()->create([
        'email' => 'jane@example.com',
        'role' => UserRole::User,
    ]);

    fakeAuthentikUser([
        'sub' => 'sub-1',
        'email' => 'jane@example.com',
        'email_verified' => true,
        'name' => 'Jane',
    ]);

    test()->get(ssoCallback())->assertRedirect('/');

    test()->assertAuthenticatedAs($existing->fresh());

    assertDatabaseCount('users', 1);
    assertDatabaseHas('users', [
        'id' => $existing->id,
        'sso_provider' => 'authentik',
        'sso_id' => 'sub-1',
    ]);
});

it('does not link or create an account on an unverified email', function () {
    bootSso();

    $existing = User::factory()->create([
        'email' => 'jane@example.com',
        'role' => UserRole::User,
    ]);

    fakeAuthentikUser([
        'sub' => 'sub-1',
        'email' => 'jane@example.com',
        'email_verified' => false,
        'name' => 'Jane',
    ]);

    test()->get(ssoCallback())
        ->assertRedirect(route('filament.admin.auth.login'));

    test()->assertGuest();

    assertDatabaseCount('users', 1);
    assertDatabaseHas('users', [
        'id' => $existing->id,
        'sso_provider' => null,
        'sso_id' => null,
    ]);
});

it('redirects to login and stays a guest on a state mismatch', function () {
    bootSso();

    $provider = Mockery::mock(AbstractProvider::class);
    $provider->shouldReceive('setScopes')->andReturnSelf();
    $provider->shouldReceive('user')->andThrow(new InvalidStateException);

    Socialite::shouldReceive('driver')->andReturn($provider);

    test()->get(ssoCallback())
        ->assertRedirect(route('filament.admin.auth.login'));

    test()->assertGuest();

    assertDatabaseCount('users', 0);
});

it('rejects an inbound callback missing code or state', function () {
    bootSso();

    test()->get(route('sso.callback', 'authentik'))
        ->assertRedirect(route('filament.admin.auth.login'));

    test()->assertGuest();
});

it('maps provider groups to the admin role', function () {
    bootSso([
        'role_mapping_enabled' => true,
        'admin_groups' => ['speedtest-admins'],
    ]);

    fakeAuthentikUser([
        'sub' => 'sub-9',
        'email' => 'boss@example.com',
        'email_verified' => true,
        'name' => 'Boss',
        'groups' => ['speedtest-admins', 'staff'],
    ]);

    test()->get(ssoCallback())->assertRedirect('/');

    assertDatabaseHas('users', [
        'email' => 'boss@example.com',
        'sso_id' => 'sub-9',
        'role' => 'admin',
    ]);
});
