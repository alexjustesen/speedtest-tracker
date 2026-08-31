<?php

use App\Actions\GetOoklaSpeedtestServers;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

describe('GetOoklaSpeedtestServers::options', function () {
    it('returns an empty array when the request fails', function () {
        Cache::flush();

        Http::fake([
            'www.speedtest.net/*' => Http::response(status: 500),
        ]);

        expect(GetOoklaSpeedtestServers::options())->toBe([]);
    });

    it('returns the id => label map on success', function () {
        Cache::flush();

        Http::fake([
            'www.speedtest.net/*' => Http::response([
                ['id' => '12345', 'sponsor' => 'Test Sponsor', 'name' => 'New York'],
            ]),
        ]);

        expect(GetOoklaSpeedtestServers::options())->toBe([
            '12345' => 'Test Sponsor (New York, 12345)',
        ]);
    });
});
