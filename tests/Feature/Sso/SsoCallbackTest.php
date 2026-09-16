<?php

use App\Enums\UserRole;
use App\Models\User;
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
    config(['services.openidconnect' => array_merge((array) config('services.openidconnect'), [
        'enabled' => true,
        'base_url' => 'https://id.test',
        'client_id' => 'my-client',
        'client_secret' => 'my-secret',
        'auto_provision' => true,
    ], $overrides)]);
}

function ssoCallback(): string
{
    return route('sso.callback', ['code' => 'test-code', 'state' => 'test-state']);
}

function fakeSsoUser(array $raw): void
{
    $socialiteUser = (new SocialiteUser)
        ->setRaw($raw)
        ->map([
            'id' => $raw['sub'] ?? null,
            'name' => $raw['name'] ?? null,
            'email' => $raw['email'] ?? null,
        ]);

    $provider = Mockery::mock(AbstractProvider::class);
    $provider->shouldReceive('user')->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->andReturn($provider);
}

it('creates and authenticates a new user on a verified email', function () {
    bootSso();

    fakeSsoUser([
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
        'sso_provider' => 'openidconnect',
        'sso_id' => 'sub-1',
        'role' => 'user',
    ]);
});

it('does not create an account when auto provisioning is off', function () {
    bootSso(['auto_provision' => false]);

    fakeSsoUser([
        'sub' => 'sub-1',
        'email' => 'jane@example.com',
        'email_verified' => true,
        'name' => 'Jane Doe',
    ]);

    test()->get(ssoCallback())
        ->assertRedirect(route('filament.admin.auth.login'));

    test()->assertGuest();

    assertDatabaseCount('users', 0);
});

it('links an existing local account on a verified email', function () {
    bootSso(['auto_provision' => false]);

    $existing = User::factory()->create([
        'email' => 'jane@example.com',
        'role' => UserRole::User,
    ]);

    fakeSsoUser([
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
        'sso_provider' => 'openidconnect',
        'sso_id' => 'sub-1',
    ]);
});

it('does not link or create an account on an unverified email', function () {
    bootSso();

    $existing = User::factory()->create([
        'email' => 'jane@example.com',
        'role' => UserRole::User,
    ]);

    fakeSsoUser([
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
    $provider->shouldReceive('user')->andThrow(new InvalidStateException);

    Socialite::shouldReceive('driver')->andReturn($provider);

    test()->get(ssoCallback())
        ->assertRedirect(route('filament.admin.auth.login'));

    test()->assertGuest();

    assertDatabaseCount('users', 0);
});

it('rejects an inbound callback missing code or state', function () {
    bootSso();

    test()->get(route('sso.callback'))
        ->assertRedirect(route('filament.admin.auth.login'));

    test()->assertGuest();
});

it('maps provider groups to the admin role', function () {
    bootSso(['admin_groups' => 'speedtest-admins']);

    fakeSsoUser([
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

it('demotes a linked user that left the admin groups', function () {
    bootSso(['admin_groups' => 'speedtest-admins']);

    $existing = User::factory()->create([
        'email' => 'boss@example.com',
        'role' => UserRole::Admin,
        'sso_provider' => 'openidconnect',
        'sso_id' => 'sub-9',
    ]);

    fakeSsoUser([
        'sub' => 'sub-9',
        'email' => 'boss@example.com',
        'email_verified' => true,
        'name' => 'Boss',
        'groups' => ['staff'],
    ]);

    test()->get(ssoCallback())->assertRedirect('/');

    assertDatabaseHas('users', [
        'id' => $existing->id,
        'role' => 'user',
    ]);
});

it('keeps the local role when no admin groups are configured', function () {
    bootSso();

    $existing = User::factory()->create([
        'email' => 'boss@example.com',
        'role' => UserRole::Admin,
        'sso_provider' => 'openidconnect',
        'sso_id' => 'sub-9',
    ]);

    fakeSsoUser([
        'sub' => 'sub-9',
        'email' => 'boss@example.com',
        'email_verified' => true,
        'name' => 'Boss',
    ]);

    test()->get(ssoCallback())->assertRedirect('/');

    assertDatabaseHas('users', [
        'id' => $existing->id,
        'role' => 'admin',
    ]);
});

it('uses the configured default role for new users', function () {
    bootSso(['default_role' => 'admin']);

    fakeSsoUser([
        'sub' => 'sub-2',
        'email' => 'first@example.com',
        'email_verified' => true,
        'name' => 'First',
    ]);

    test()->get(ssoCallback())->assertRedirect('/');

    assertDatabaseHas('users', [
        'email' => 'first@example.com',
        'role' => 'admin',
    ]);
});
