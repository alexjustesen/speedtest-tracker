<?php

it('renders all sections by default', function () {
    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertSee('Speed', false);
    $response->assertSee('Ping', false);
    $response->assertSee('Latency (IQM)', false);
    $response->assertSee('Jitter', false);
});

it('includes section management UI in filter modal', function () {
    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertSee('Manage Sections', false);
    $response->assertSee('Drag to reorder', false);
    $response->assertSee('Reset to Default Order', false);
});

it('includes Alpine sort directive in rendered view', function () {
    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertSee('x-sort', false);
    $response->assertSee('sectionManager()', false);
    $response->assertSee('dashboardSections()', false);
});

it('includes all four sections in sortable list', function () {
    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertSee("id: 'speed'", false);
    $response->assertSee("id: 'ping'", false);
    $response->assertSee("id: 'latency'", false);
    $response->assertSee("id: 'jitter'", false);
});

it('includes localStorage preference loading logic', function () {
    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertSee("localStorage.getItem('metrics-dashboard-preferences')", false);
});

it('includes default preferences fallback', function () {
    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertSee("sectionOrder: ['speed', 'ping', 'latency', 'jitter']", false);
    $response->assertSee('hiddenSections: []', false);
});

it('dispatches event when preferences change', function () {
    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertSee("window.dispatchEvent(new CustomEvent('dashboard-preferences-changed'", false);
});

it('includes validation for corrupted preferences', function () {
    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertSee('try {', false);
    $response->assertSee('catch (e)', false);
});
