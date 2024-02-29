<?php

namespace App\Actions\Speedtests;

use App\Enums\ResultStatus;
use App\Events\SpeedtestStarted;
use App\Jobs\Speedtests\ExecuteOoklaSpeedtest;
use App\Models\Result;
use Lorisleiva\Actions\Concerns\AsAction;

class RunOoklaSpeedtest
{
    use AsAction;

    public function handle(?int $serverId = null, bool $scheduled = false): void
    {
        $result = Result::create([
            'service' => 'ookla',
            'status' => ResultStatus::Started,
            'scheduled' => $scheduled,
        ]);

        SpeedtestStarted::dispatch($result);

        ExecuteOoklaSpeedtest::dispatch(result: $result, serverId: $serverId);
    }
}
