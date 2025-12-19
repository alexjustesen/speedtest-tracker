<?php

use App\Livewire\MetricsDashboard;
use Livewire\Livewire;

it('initializes with default date range of last 24 hours', function () {
    $component = Livewire::test(MetricsDashboard::class);

    $startDate = now()->subDay()->format('Y-m-d');
    $endDate = now()->format('Y-m-d');

    expect($component->get('startDate'))->toBe($startDate);
    expect($component->get('endDate'))->toBe($endDate);
});

it('initializes with default scheduled filter set to all', function () {
    $component = Livewire::test(MetricsDashboard::class);

    expect($component->get('scheduledFilter'))->toBe('all');
});

it('applies filters and dispatches event when valid dates are provided', function () {
    $component = Livewire::test(MetricsDashboard::class);

    $component->set('startDate', '2025-01-01');
    $component->set('endDate', '2025-01-15');

    $component->call('applyFilters');

    $component->assertDispatched('charts-updated');
});

it('validates that start date is before or equal to end date', function () {
    $component = Livewire::test(MetricsDashboard::class);

    $component->set('startDate', '2025-01-15');
    $component->set('endDate', '2025-01-01');

    $component->call('applyFilters');

    $component->assertHasErrors(['startDate']);
});

it('validates that end date cannot be in the future', function () {
    $component = Livewire::test(MetricsDashboard::class);

    $component->set('startDate', now()->format('Y-m-d'));
    $component->set('endDate', now()->addDay()->format('Y-m-d'));

    $component->call('applyFilters');

    $component->assertHasErrors(['endDate']);
});

it('validates that start date cannot be in the future', function () {
    $component = Livewire::test(MetricsDashboard::class);

    $component->set('startDate', now()->addDay()->format('Y-m-d'));
    $component->set('endDate', now()->addDays(2)->format('Y-m-d'));

    $component->call('applyFilters');

    $component->assertHasErrors(['startDate']);
});

it('resets filters to default last 24 hours', function () {
    $component = Livewire::test(MetricsDashboard::class);

    $component->set('startDate', '2025-01-01');
    $component->set('endDate', '2025-01-15');

    $component->call('resetFilters');

    $expectedStartDate = now()->subDay()->format('Y-m-d');
    $expectedEndDate = now()->format('Y-m-d');

    expect($component->get('startDate'))->toBe($expectedStartDate);
    expect($component->get('endDate'))->toBe($expectedEndDate);
});

it('resets scheduled filter to all', function () {
    $component = Livewire::test(MetricsDashboard::class);

    $component->set('scheduledFilter', 'scheduled');

    $component->call('resetFilters');

    expect($component->get('scheduledFilter'))->toBe('all');
});

it('can set scheduled filter to scheduled', function () {
    $component = Livewire::test(MetricsDashboard::class);

    $component->set('scheduledFilter', 'scheduled');

    expect($component->get('scheduledFilter'))->toBe('scheduled');
});

it('can set scheduled filter to unscheduled', function () {
    $component = Livewire::test(MetricsDashboard::class);

    $component->set('scheduledFilter', 'unscheduled');

    expect($component->get('scheduledFilter'))->toBe('unscheduled');
});

it('does not include localStorage persistence code in rendered view', function () {
    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertDontSee('localStorage.getItem(\'metrics-date-range\')', false);
    $response->assertDontSee('localStorage.setItem(\'metrics-date-range\'', false);
});

it('includes filter modal elements in rendered view', function () {
    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertSee('Filter by Date', false);
    $response->assertSee('flux:modal.trigger', false);
    $response->assertSee('name="filterModal"', false);
});

it('includes max date constraint in date inputs', function () {
    $response = $this->get(route('dashboard'));
    $today = now()->format('Y-m-d');

    $response->assertSuccessful();
    $response->assertSee('max="'.$today.'"', false);
});

it('includes scheduled status filter in rendered view', function () {
    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertSee('Scheduled Status', false);
    $response->assertSee('Only scheduled', false);
    $response->assertSee('Only unscheduled', false);
});
