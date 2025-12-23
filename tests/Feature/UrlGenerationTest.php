<?php

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    Cache::flush();
});

describe('url generation with subpath', function () {
    test('AppServiceProvider configureUrl sets URL origin from APP_URL config', function () {
        // First, reset the URL generator to a known state
        URL::useOrigin('http://localhost');

        // Set APP_URL config to a subpath URL
        config()->set('app.url', 'https://myserver.com/speedtest');

        // Create a new instance of AppServiceProvider and call boot
        // This simulates what happens when the app boots with APP_URL set
        $provider = new AppServiceProvider(app());
        $provider->boot();

        // Verify that route() now generates URLs with the subpath
        // Note: scheme may be http in test environment since forceScheme requires non-local + FORCE_HTTPS
        expect(route('getting-started'))->toContain('/speedtest/getting-started');
        expect(route('home'))->toContain('myserver.com/speedtest');
    });

    test('AppServiceProvider configureUrl sets URL origin for url() helper', function () {
        URL::useOrigin('http://localhost');
        config()->set('app.url', 'https://myserver.com/speedtest');

        $provider = new AppServiceProvider(app());
        $provider->boot();

        expect(url('/admin'))->toContain('/speedtest/admin');
        expect(url('/getting-started'))->toContain('/speedtest/getting-started');
    });
});

describe('url generation without subpath', function () {
    test('routes generate plain paths when APP_URL has no subpath', function () {
        // Set APP_URL to a domain without a subpath
        URL::useOrigin('http://localhost');
        config()->set('app.url', 'https://myserver.com');

        $provider = new AppServiceProvider(app());
        $provider->boot();

        // URLs should not have any subpath prefix - just the plain route path
        expect(route('getting-started'))->toContain('myserver.com/getting-started');
        expect(route('home'))->toContain('myserver.com');
        // Verify no subpath is present
        expect(route('getting-started'))->not->toContain('myserver.com/speedtest');
    });

    test('url() helper generates plain paths when APP_URL has no subpath', function () {
        URL::useOrigin('http://localhost');
        config()->set('app.url', 'https://myserver.com');

        $provider = new AppServiceProvider(app());
        $provider->boot();

        expect(url('/admin'))->toContain('myserver.com/admin');
        expect(url('/getting-started'))->toContain('myserver.com/getting-started');
        // Verify no subpath is present
        expect(url('/admin'))->not->toContain('myserver.com/speedtest');
    });
});
