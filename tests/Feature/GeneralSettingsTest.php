<?php

use App\Enums\UserRole;
use App\Filament\Pages\Settings\General;
use App\Models\User;
use App\Settings\GeneralSettings;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    $this->user = User::factory()->create(['role' => UserRole::User]);
});

describe('access', function () {
    it('renders for admin users', function () {
        $this->actingAs($this->admin);

        Livewire::test(General::class)
            ->assertSuccessful();
    });

    it('denies access to non-admin users', function () {
        $this->actingAs($this->user);

        expect(General::canAccess())->toBeFalse();
    });
});

describe('form', function () {
    it('loads the current default chart range into the form', function () {
        $this->actingAs($this->admin);

        $settings = app(GeneralSettings::class);

        Livewire::test(General::class)
            ->assertFormSet([
                'default_chart_range' => $settings->default_chart_range,
            ]);
    });

    it('saves the updated default chart range to the database', function () {
        $this->actingAs($this->admin);

        Livewire::test(General::class)
            ->fillForm(['default_chart_range' => 'week'])
            ->call('save')
            ->assertHasNoFormErrors();

        app()->forgetInstance(GeneralSettings::class);

        expect(app(GeneralSettings::class)->default_chart_range)->toBe('week');
    });

    it('requires a default chart range', function () {
        $this->actingAs($this->admin);

        Livewire::test(General::class)
            ->fillForm(['default_chart_range' => null])
            ->call('save')
            ->assertHasFormErrors(['default_chart_range' => 'required']);
    });
});
