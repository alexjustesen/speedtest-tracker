<?php

namespace App\Actions\Ookla;

use App\Enums\ResultService;
use App\Enums\ResultStatus;
use App\Events\SpeedtestWaiting;
use App\Jobs\Ookla\ProcessSpeedtestBatch;
use App\Models\Result;
use Lorisleiva\Actions\Concerns\AsAction;

class StartSpeedtest
{
    use AsAction;

    public function handle(bool $scheduled = false, ?int $serverId = null): Result
    {
        $result = Result::create([
            'data->server->id' => $serverId,
            'service' => ResultService::Ookla,
            'status' => ResultStatus::Waiting,
            'scheduled' => $scheduled,
        ]);

        ProcessSpeedtestBatch::dispatch(
            result: $result,
        );

        SpeedtestWaiting::dispatch($result);

        return $result;
    }
}
