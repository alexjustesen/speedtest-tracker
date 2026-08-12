<?php

use App\Enums\UserRole;
use App\Filament\Pages\Settings\SingleSignOn;
use App\Models\User;
use App\Sso\Contracts\SsoConnectionTester;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('reports success when the provider accepts the client', function () {
    Http::fake([
        '*/application/o/token/' => Http::response(['error' => 'unsupported_grant_type'], 400),
    ]);

    $result = app(SsoConnectionTester::class)
        ->test('authentik', 'https://authentik.test', 'my-client', 'my-secret');

    expect($result->ok)->toBeTrue()
        ->and($result->message)->toBe(__('settings/sso.test_success'));
});

it('reports failure when the client credentials are rejected', function () {
    Http::fake([
        '*/application/o/token/' => Http::response(['error' => 'invalid_client'], 401),
    ]);

    $result = app(SsoConnectionTester::class)
        ->test('authentik', 'https://authentik.test', 'my-client', 'wrong-secret');

    expect($result->ok)->toBeFalse()
        ->and($result->message)->toBe(__('settings/sso.test_failed'));
});

it('reports the provider is unreachable on a transport error', function () {
    Http::fake(function () {
        throw new ConnectionException('could not connect');
    });

    $result = app(SsoConnectionTester::class)
        ->test('authentik', 'https://authentik.test', 'my-client', 'my-secret');

    expect($result->ok)->toBeFalse()
        ->and($result->message)->toBe(__('settings/sso.test_unreachable'));
});

it('reports missing config when required fields are blank', function () {
    $result = app(SsoConnectionTester::class)
        ->test('authentik', null, null, null);

    expect($result->ok)->toBeFalse()
        ->and($result->message)->toBe(__('settings/sso.test_missing_config'));
});

it('reveals the connection test once SSO is enabled in the form', function () {
    actingAs(User::factory()->create(['role' => UserRole::Admin]));

    Livewire::test(SingleSignOn::class)
        ->assertDontSee(__('settings/sso.test_connection'))
        ->fillForm(['enabled' => true])
        ->assertSee(__('settings/sso.test_connection'));
});

it('targets the active provider token endpoint when testing', function () {
    Http::fake([
        '*/application/o/token/' => Http::response(['error' => 'unsupported_grant_type'], 400),
    ]);

    app(SsoConnectionTester::class)
        ->test('authentik', 'https://typed-in-form.test', 'typed-client', 'typed-secret');

    Http::assertSent(fn ($request) => $request->url() === 'https://typed-in-form.test/application/o/token/');
});
