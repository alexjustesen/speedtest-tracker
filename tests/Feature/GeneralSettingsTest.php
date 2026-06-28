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

        $this->get(General::getUrl())->assertForbidden();
    });
});

describe('form', function () {
    it('loads current settings into the form', function () {
        $this->actingAs($this->admin);

        $settings = app(GeneralSettings::class);

        Livewire::test(General::class)
            ->assertFormSet([
                'display_timezone' => $settings->display_timezone,
                'datetime_format' => $settings->datetime_format,
                'chart_datetime_format' => $settings->chart_datetime_format,
                'chart_begin_at_zero' => $settings->chart_begin_at_zero,
                'default_chart_range' => $settings->default_chart_range,
                'prune_results_older_than' => $settings->prune_results_older_than,
                'speedtest_external_ip_url' => $settings->speedtest_external_ip_url,
                'speedtest_internet_check_hostname' => $settings->speedtest_internet_check_hostname,
            ]);
    });

    it('saves updated settings to the database', function () {
        $this->actingAs($this->admin);

        Livewire::test(General::class)
            ->fillForm([
                'display_timezone' => 'Europe/Berlin',
                'datetime_format' => 'd.m.Y H:i',
                'chart_datetime_format' => 'd.m H:i',
                'chart_begin_at_zero' => false,
                'default_chart_range' => 'week',
                'prune_results_older_than' => 30,
                'speedtest_external_ip_url' => 'https://checkip.amazonaws.com',
                'speedtest_internet_check_hostname' => 'checkip.amazonaws.com',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        app()->forgetInstance(GeneralSettings::class);
        $settings = app(GeneralSettings::class);

        expect($settings->display_timezone)->toBe('Europe/Berlin')
            ->and($settings->datetime_format)->toBe('d.m.Y H:i')
            ->and($settings->chart_datetime_format)->toBe('d.m H:i')
            ->and($settings->chart_begin_at_zero)->toBeFalse()
            ->and($settings->default_chart_range)->toBe('week')
            ->and($settings->prune_results_older_than)->toBe(30)
            ->and($settings->speedtest_external_ip_url)->toBe('https://checkip.amazonaws.com')
            ->and($settings->speedtest_internet_check_hostname)->toBe('checkip.amazonaws.com');
    });

    it('requires all fields', function () {
        $this->actingAs($this->admin);

        Livewire::test(General::class)
            ->fillForm([
                'display_timezone' => null,
                'datetime_format' => null,
                'chart_datetime_format' => null,
                'default_chart_range' => null,
                'prune_results_older_than' => null,
                'speedtest_external_ip_url' => null,
                'speedtest_internet_check_hostname' => null,
            ])
            ->call('save')
            ->assertHasFormErrors([
                'display_timezone' => 'required',
                'datetime_format' => 'required',
                'chart_datetime_format' => 'required',
                'default_chart_range' => 'required',
                'prune_results_older_than' => 'required',
                'speedtest_external_ip_url' => 'required',
                'speedtest_internet_check_hostname' => 'required',
            ]);
    });

    it('validates the external ip url is a valid url', function () {
        $this->actingAs($this->admin);

        Livewire::test(General::class)
            ->fillForm(['speedtest_external_ip_url' => 'not-a-url'])
            ->call('save')
            ->assertHasFormErrors(['speedtest_external_ip_url' => 'url']);
    });

    it('validates prune_results_older_than is numeric and at least 0', function () {
        $this->actingAs($this->admin);

        Livewire::test(General::class)
            ->fillForm(['prune_results_older_than' => -1])
            ->call('save')
            ->assertHasFormErrors(['prune_results_older_than' => 'min']);
    });
});
