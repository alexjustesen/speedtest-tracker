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
