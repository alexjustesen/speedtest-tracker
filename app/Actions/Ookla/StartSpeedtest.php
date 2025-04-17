<?php

namespace App\Actions\Ookla;

use App\Enums\ResultService;
use App\Enums\ResultStatus;
use App\Events\SpeedtestStarted;
use App\Jobs\Ookla\ProcessSpeedtestBatch;
use App\Models\Result;
use App\Models\Test;
use Lorisleiva\Actions\Concerns\AsAction;

class StartSpeedtest
{
    use AsAction;

    public function handle(bool $scheduled = false, ?Test $test = null, array $scheduleOptions = [], ?int $serverId = null): void
    {
        $result = Result::create([
            'test_id' => $test?->id,
            'data->server->id' => $serverId,
            'service' => ResultService::Ookla,
            'status' => ResultStatus::Started,
            'scheduled' => $scheduled,
        ]);

        // Dispatch the job to handle the server selection and speedtest process
        ProcessSpeedtestBatch::dispatch(
            result: $result,
        );

        // Fire event that the speedtest has started
        SpeedtestStarted::dispatch($result);
    }
}
