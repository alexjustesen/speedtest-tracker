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
            ->fillForm(['default_chart_range' => 7])
            ->call('save')
            ->assertHasNoFormErrors();

        app()->forgetInstance(GeneralSettings::class);

        expect(app(GeneralSettings::class)->default_chart_range)->toBe(7);
    });

    it('loads the current connectivity settings into the form', function () {
        $this->actingAs($this->admin);

        $settings = app(GeneralSettings::class);

        Livewire::test(General::class)
            ->assertFormSet([
                'external_ip_url' => $settings->external_ip_url,
                'internet_check_hostname' => $settings->internet_check_hostname,
            ]);
    });

    it('saves the updated connectivity settings to the database', function () {
        $this->actingAs($this->admin);

        Livewire::test(General::class)
            ->fillForm([
                'external_ip_url' => 'https://ifconfig.me',
                'internet_check_hostname' => 'one.one.one.one',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        app()->forgetInstance(GeneralSettings::class);

        $settings = app(GeneralSettings::class);

        expect($settings->external_ip_url)->toBe('https://ifconfig.me')
            ->and($settings->internet_check_hostname)->toBe('one.one.one.one');
    });

    it('validates the connectivity settings', function () {
        $this->actingAs($this->admin);

        Livewire::test(General::class)
            ->fillForm([
                'external_ip_url' => 'not-a-url',
                'internet_check_hostname' => null,
            ])
            ->call('save')
            ->assertHasFormErrors([
                'external_ip_url' => 'url',
                'internet_check_hostname' => 'required',
            ]);
    });

    it('requires a default chart range', function () {
        $this->actingAs($this->admin);

        Livewire::test(General::class)
            ->fillForm(['default_chart_range' => null])
            ->call('save')
            ->assertHasFormErrors(['default_chart_range' => 'required']);
    });
});
