<?php

use App\Livewire\DateRangeFilter;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(DateRangeFilter::class)
        ->assertStatus(200);
});

it('initializes with default date range from config', function () {
    config(['speedtest.default_chart_range' => '24h']);

    Livewire::test(DateRangeFilter::class)
        ->assertSet('dateFrom', now()->subDay()->startOfDay()->toDateTimeString())
        ->assertSet('dateTo', now()->endOfDay()->toDateTimeString());
});

it('has filament form with date pickers', function () {
    $component = Livewire::test(DateRangeFilter::class);

    expect($component->instance()->form)->not->toBeNull();
});

it('broadcasts filter on mount', function () {
    Livewire::test(DateRangeFilter::class)
        ->assertDispatched('date-range-updated');
});

it('updates dateFrom and broadcasts when changed', function () {
    $newDate = now()->subWeek()->toDateTimeString();

    Livewire::test(DateRangeFilter::class)
        ->set('dateFrom', $newDate)
        ->assertSet('dateFrom', $newDate);
});

it('updates dateTo and broadcasts when changed', function () {
    $newDate = now()->toDateTimeString();

    Livewire::test(DateRangeFilter::class)
        ->set('dateTo', $newDate)
        ->assertSet('dateTo', $newDate);
});
