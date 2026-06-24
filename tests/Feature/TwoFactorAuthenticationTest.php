<?php

use App\Models\User;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Auth\Pages\EditProfile;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

describe('User model', function () {
    it('implements the required MFA interfaces', function () {
        $user = User::factory()->create();

        expect($user)->toBeInstanceOf(HasAppAuthentication::class)
            ->toBeInstanceOf(HasAppAuthenticationRecovery::class)
            ->toBeInstanceOf(HasEmailAuthentication::class);
    });

    it('has the app_authentication_secret column defaulting to null', function () {
        $user = User::factory()->create(['app_authentication_secret' => null]);

        expect($user->getAppAuthenticationSecret())->toBeNull();
    });

    it('has the app_authentication_recovery_codes column defaulting to null', function () {
        $user = User::factory()->create(['app_authentication_recovery_codes' => null]);

        expect($user->getAppAuthenticationRecoveryCodes())->toBeNull();
    });

    it('has email authentication disabled by default', function () {
        $user = User::factory()->create();

        expect($user->hasEmailAuthentication())->toBeFalse();
    });
});

describe('Profile page', function () {
    it('loads successfully for an authenticated user', function () {
        $user = User::factory()->create();

        actingAs($user);

        Livewire::test(EditProfile::class)
            ->assertOk();
    });
});
