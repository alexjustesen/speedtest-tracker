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
                'chart_default_range' => $settings->chart_default_range,
            ]);
    });

    it('saves the updated default chart range to the database', function () {
        $this->actingAs($this->admin);

        Livewire::test(General::class)
            ->fillForm(['chart_default_range' => 7])
            ->call('save')
            ->assertHasNoFormErrors();

        app()->forgetInstance(GeneralSettings::class);

        expect(app(GeneralSettings::class)->chart_default_range)->toBe(7);
    });

    it('requires a default chart range', function () {
        $this->actingAs($this->admin);

        Livewire::test(General::class)
            ->fillForm(['chart_default_range' => null])
            ->call('save')
            ->assertHasFormErrors(['chart_default_range' => 'required']);
    });

    it('loads the current chart display settings into the form', function () {
        $this->actingAs($this->admin);

        $settings = app(GeneralSettings::class);

        Livewire::test(General::class)
            ->assertFormSet([
                'chart_begin_at_zero' => $settings->chart_begin_at_zero,
                'chart_datetime_format' => $settings->chart_datetime_format,
                'chart_only_show_avg_latency' => $settings->chart_only_show_avg_latency,
            ]);
    });

    it('saves the updated chart display settings to the database', function () {
        $this->actingAs($this->admin);

        Livewire::test(General::class)
            ->fillForm([
                'chart_begin_at_zero' => false,
                'chart_datetime_format' => 'Y-m-d H:i',
                'chart_only_show_avg_latency' => true,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        app()->forgetInstance(GeneralSettings::class);

        $settings = app(GeneralSettings::class);

        expect($settings->chart_begin_at_zero)->toBeFalse()
            ->and($settings->chart_datetime_format)->toBe('Y-m-d H:i')
            ->and($settings->chart_only_show_avg_latency)->toBeTrue();
    });

    it('requires a chart datetime format', function () {
        $this->actingAs($this->admin);

        Livewire::test(General::class)
            ->fillForm(['chart_datetime_format' => null])
            ->call('save')
            ->assertHasFormErrors(['chart_datetime_format' => 'required']);
    });
});
