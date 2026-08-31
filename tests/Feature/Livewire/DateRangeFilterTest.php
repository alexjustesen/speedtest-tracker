<?php

use App\Livewire\DateRangeFilter;
use App\Models\User;
use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(User::factory()->create());
    Carbon::setTestNow(Carbon::parse('2026-07-23 12:00:00'));
});

afterEach(function () {
    Carbon::setTestNow();
});

it('mounts with the admin-configured default range and broadcasts it', function () {
    app(GeneralSettings::class)->fill(['default_chart_range' => 7])->save();

    Livewire::test(DateRangeFilter::class)
        ->assertOk()
        ->assertDispatched('date-range-updated', function (string $name, array $params) {
            return $params[0]['dateFrom'] === now()->subDays(7)->toDateTimeString()
                && $params[0]['dateTo'] === now()->toDateTimeString();
        });
});

it('broadcasts an updated range when the start date changes', function () {
    app(GeneralSettings::class)->fill(['default_chart_range' => 1])->save();

    Livewire::test(DateRangeFilter::class)
        ->set('dateFrom', now()->subHours(2)->toDateTimeString())
        ->assertDispatched('date-range-updated');
});

it('normalizes the range so the end date is never before the start date', function () {
    app(GeneralSettings::class)->fill(['default_chart_range' => 1])->save();

    Livewire::test(DateRangeFilter::class)
        ->set('dateFrom', now()->toDateTimeString())
        ->set('dateTo', now()->subDay()->toDateTimeString())
        ->assertDispatched('date-range-updated', function (string $name, array $params) {
            return $params[0]['dateFrom'] === now()->subDay()->toDateTimeString()
                && $params[0]['dateTo'] === now()->toDateTimeString();
        });
});

it('broadcasts the last 24 hours range when the 24h preset is selected', function () {
    Livewire::test(DateRangeFilter::class)
        ->set('relativeRange', '24h')
        ->assertDispatched('date-range-updated', function (string $name, array $params) {
            return $params[0]['dateFrom'] === now()->subHours(24)->toDateTimeString()
                && $params[0]['dateTo'] === now()->toDateTimeString();
        });
});

it('broadcasts the last 7 days range when the 7d preset is selected', function () {
    Livewire::test(DateRangeFilter::class)
        ->set('relativeRange', '7d')
        ->assertDispatched('date-range-updated', function (string $name, array $params) {
            return $params[0]['dateFrom'] === now()->subDays(7)->toDateTimeString()
                && $params[0]['dateTo'] === now()->toDateTimeString();
        });
});

it('broadcasts the last 30 days range when the 30d preset is selected', function () {
    Livewire::test(DateRangeFilter::class)
        ->set('relativeRange', '30d')
        ->assertDispatched('date-range-updated', function (string $name, array $params) {
            return $params[0]['dateFrom'] === now()->subDays(30)->toDateTimeString()
                && $params[0]['dateTo'] === now()->toDateTimeString();
        });
});

it('updates the date pickers to reflect the selected preset', function () {
    Livewire::test(DateRangeFilter::class)
        ->set('relativeRange', '7d')
        ->assertSet('dateFrom', now()->subDays(7)->toDateTimeString())
        ->assertSet('dateTo', now()->toDateTimeString());
});

it('clears the selected preset when a date picker is edited manually', function () {
    Livewire::test(DateRangeFilter::class)
        ->set('relativeRange', '7d')
        ->assertSet('relativeRange', '7d')
        ->set('dateFrom', now()->subHours(3)->toDateTimeString())
        ->assertSet('relativeRange', null);
});
