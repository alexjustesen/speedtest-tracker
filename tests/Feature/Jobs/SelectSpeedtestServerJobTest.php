<?php

use App\Jobs\Ookla\SelectSpeedtestServerJob;
use App\Models\Result;
use App\Models\Speedtest;

function selectServerFor(Speedtest $speedtest, bool $scheduled = true): Result
{
    $result = Result::factory()->create([
        'speedtest_id' => $speedtest->id,
        'scheduled' => $scheduled,
        'data' => [],
    ]);

    (new SelectSpeedtestServerJob($result))->handle();

    return $result->fresh();
}

describe('SelectSpeedtestServerJob', function () {
    it('picks one of the preferred servers for a scheduled test', function () {
        $speedtest = Speedtest::factory()->prefer(['111', '222'])->create();

        expect(selectServerFor($speedtest)->data['server']['id'])->toBeIn([111, 222]);
    });

    it('ignores preferred servers for a non-scheduled test', function () {
        $speedtest = Speedtest::factory()->prefer(['111'])->create();

        expect(data_get(selectServerFor($speedtest, scheduled: false)->data, 'server.id'))->toBeNull();
    });

    it('does not pick a server in automatic mode', function () {
        $speedtest = Speedtest::factory()->create();

        expect(data_get(selectServerFor($speedtest)->data, 'server.id'))->toBeNull();
    });

    it('keeps a server id that is already set', function () {
        $speedtest = Speedtest::factory()->prefer(['111'])->create();

        $result = Result::factory()->create([
            'speedtest_id' => $speedtest->id,
            'scheduled' => true,
            'data' => ['server' => ['id' => 999]],
        ]);

        (new SelectSpeedtestServerJob($result))->handle();

        expect($result->fresh()->data['server']['id'])->toBe(999);
    });
});
