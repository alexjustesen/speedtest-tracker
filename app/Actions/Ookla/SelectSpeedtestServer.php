<?php

namespace App\Actions\Ookla;

use Illuminate\Support\Arr;
use Lorisleiva\Actions\Concerns\AsAction;

class SelectSpeedtestServer
{
    use AsAction;

    public function handle(): string
    {
        $servers = config('speedtest.servers');

        if (blank($servers)) {
            return '';
        }

        $servers = array_filter(
            array_map(
                'trim',
                explode(',', $servers),
            ),
        );

        if (count($servers) < 1) {
            return '';
        }

        return Arr::random($servers);
    }
}
