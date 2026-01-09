<?php

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    Cache::flush();
});

describe('url generation with subpath', function () {
    test('route() helper generates URLs with subpath', function () {
        URL::useOrigin('http://localhost');
        config()->set('app.url', 'https://myserver.com/speedtest');

        (new AppServiceProvider(app()))->boot();

        $url = route('getting-started');

        expect($url)->toContain('/speedtest/');
    });

    test('url() helper respects subpath configuration', function () {
        URL::useOrigin('http://localhost');
        config()->set('app.url', 'https://myserver.com/speedtest');

        (new AppServiceProvider(app()))->boot();

        $url = url('/');

        expect($url)->toContain('/speedtest');
    });
});

describe('url generation without subpath', function () {
    test('route() helper generates plain path when APP_URL has no subpath', function () {
        URL::useOrigin('http://localhost');
        config()->set('app.url', 'https://myserver.com');

        (new AppServiceProvider(app()))->boot();

        $url = route('getting-started');

        expect($url)->not->toContain('/speedtest/');
    });

    test('url() helper works correctly without subpath', function () {
        URL::useOrigin('http://localhost');
        config()->set('app.url', 'https://myserver.com');

        (new AppServiceProvider(app()))->boot();

        $url = url('/');

        expect($url)->not->toContain('/speedtest');
    });
});
