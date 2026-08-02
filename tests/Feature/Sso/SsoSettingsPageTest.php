<?php

use App\Enums\UserRole;
use App\Filament\Pages\Settings\SingleSignOn;
use App\Models\User;
use App\Settings\SsoSettings;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('only allows administrators to access the page', function () {
    expect(SingleSignOn::canAccess())->toBeFalse();

    actingAs(User::factory()->create(['role' => UserRole::User]));
    expect(SingleSignOn::canAccess())->toBeFalse();

    actingAs(User::factory()->create(['role' => UserRole::Admin]));
    expect(SingleSignOn::canAccess())->toBeTrue();
});

it('saves the configuration from the settings page', function () {
    actingAs(User::factory()->create(['role' => UserRole::Admin]));

    Livewire::test(SingleSignOn::class)
        ->assertSuccessful()
        ->fillForm([
            'enabled' => true,
            'provider' => 'authentik',
            'base_url' => 'https://authentik.test',
            'client_id' => 'my-client',
            'client_secret' => 'my-super-secret',
            'default_role' => UserRole::User->value,
        ])
        ->call('save');

    $settings = app(SsoSettings::class);

    expect($settings->enabled)->toBeTrue()
        ->and($settings->provider)->toBe('authentik')
        ->and($settings->client_id)->toBe('my-client')
        ->and($settings->base_url)->toBe('https://authentik.test')
        ->and($settings->client_secret)->toBe('my-super-secret');
});

it('encrypts the client secret at rest', function () {
    $settings = app(SsoSettings::class);
    $settings->client_secret = 'my-super-secret';
    $settings->save();

    $payload = DB::table('settings')
        ->where('group', 'sso')
        ->where('name', 'client_secret')
        ->value('payload');

    expect($payload)->not->toContain('my-super-secret')
        ->and(Crypt::decrypt(json_decode($payload, true)))->toBe('my-super-secret');
});
