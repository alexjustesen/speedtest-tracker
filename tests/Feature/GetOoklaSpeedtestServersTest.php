<?php

use App\Actions\GetOoklaSpeedtestServers;
use Illuminate\Support\Facades\Http;

describe('GetOoklaSpeedtestServers', function () {
    describe('search', function () {
        test('returns empty array when query is shorter than 3 characters', function () {
            Http::fake();

            $result = GetOoklaSpeedtestServers::search('ab');

            expect($result)->toBe([]);
            Http::assertNothingSent();
        });

        test('returns formatted servers matching the search query', function () {
            Http::fake([
                'www.speedtest.net/api/js/servers*' => Http::response([
                    ['id' => 12345, 'sponsor' => 'Acme ISP', 'name' => 'London', 'host' => 'acme.example.com', 'country' => 'UK'],
                    ['id' => 67890, 'sponsor' => 'BetaNet', 'name' => 'Manchester', 'host' => 'beta.example.com', 'country' => 'UK'],
                ]),
            ]);

            $result = GetOoklaSpeedtestServers::search('London');

            expect($result)->toBe([
                12345 => 'Acme ISP (London, 12345)',
                67890 => 'BetaNet (Manchester, 67890)',
            ]);
        });

        test('sends the search term to the Ookla API', function () {
            Http::fake([
                'www.speedtest.net/api/js/servers*' => Http::response([]),
            ]);

            GetOoklaSpeedtestServers::search('Paris');

            Http::assertSent(function ($request) {
                return str_contains($request->url(), 'search=Paris');
            });
        });

        test('returns empty array when API response is not a list of servers', function () {
            Http::fake([
                'www.speedtest.net/api/js/servers*' => Http::response(['⚠️ error message']),
            ]);

            $result = GetOoklaSpeedtestServers::search('London');

            expect($result)->toBe([]);
        });

        test('returns empty array when API returns empty list', function () {
            Http::fake([
                'www.speedtest.net/api/js/servers*' => Http::response([]),
            ]);

            $result = GetOoklaSpeedtestServers::search('Nowhere');

            expect($result)->toBe([]);
        });
    });

    describe('fetch', function () {
        test('passes search parameter to API when provided', function () {
            Http::fake([
                'www.speedtest.net/api/js/servers*' => Http::response([]),
            ]);

            GetOoklaSpeedtestServers::fetch('Berlin');

            Http::assertSent(function ($request) {
                return str_contains($request->url(), 'search=Berlin');
            });
        });

        test('does not pass search parameter when null', function () {
            Http::fake([
                'www.speedtest.net/api/js/servers*' => Http::response([]),
            ]);

            GetOoklaSpeedtestServers::fetch();

            Http::assertSent(function ($request) {
                return ! str_contains($request->url(), 'search=');
            });
        });
    });
});
