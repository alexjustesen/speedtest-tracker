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

it('does not include localStorage persistence code in rendered view', function () {
    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertDontSee('localStorage.getItem(\'metrics-date-range\')', false);
    $response->assertDontSee('localStorage.setItem(\'metrics-date-range\'', false);
});

it('includes display settings modal in rendered view', function () {
    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertSee('flux:modal.trigger', false);
    $response->assertSee('name="displaySettingsModal"', false);
    $response->assertSee('Manage Sections', false);
    $response->assertSee('Uncheck to hide sections', false);
});

it('does not include sorting functionality in rendered view', function () {
    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertDontSee('x-sort', false);
    $response->assertDontSee('x-sort:item', false);
    $response->assertDontSee('x-sort:handle', false);
    $response->assertDontSee('grip-vertical', false);
    $response->assertDontSee('handleSort', false);
});

it('includes max date constraint in date inputs', function () {
    $response = $this->get(route('dashboard'));
    $today = now()->format('Y-m-d');

    $response->assertSuccessful();
    $response->assertSee('max="'.$today.'"', false);
});
