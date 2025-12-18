<?php

use App\Livewire\MetricsDashboard;
use Livewire\Livewire;

it('renders with default date range when no URL parameter', function () {
    $component = Livewire::test(MetricsDashboard::class);

    expect($component->get('dateRange'))->toBe('month');
});

it('respects URL parameter over default', function () {
    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'today']);

    expect($component->get('dateRange'))->toBe('today');
});

it('accepts week as date range from URL parameter', function () {
    $component = Livewire::test(MetricsDashboard::class, ['dateRange' => 'week']);

    expect($component->get('dateRange'))->toBe('week');
});

it('updates date range when updateDateRange method is called', function () {
    $component = Livewire::test(MetricsDashboard::class);

    expect($component->get('dateRange'))->toBe('month');

    $component->call('updateDateRange', 'week');

    expect($component->get('dateRange'))->toBe('week');
});

it('dispatches charts-updated event when date range is updated', function () {
    $component = Livewire::test(MetricsDashboard::class);

    $component->call('updateDateRange', 'today');

    $component->assertDispatched('charts-updated');
});

it('includes localStorage persistence code in rendered view', function () {
    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertSee('localStorage.getItem(\'metrics-date-range\')', false);
    $response->assertSee('localStorage.setItem(\'metrics-date-range\'', false);
});

it('includes date range validation in rendered view', function () {
    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertSee('validRanges = [\'today\', \'week\', \'month\']', false);
});

it('includes Alpine x-data component in rendered view', function () {
    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertSee('x-data', false);
    $response->assertSee('$wire.set(\'dateRange\'', false);
    $response->assertSee('$wire.$watch(\'dateRange\'', false);
});
