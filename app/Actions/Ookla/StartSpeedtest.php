<?php

namespace App\Actions\Ookla;

use App\Enums\ResultService;
use App\Enums\ResultStatus;
use App\Events\SpeedtestStarted;
use App\Jobs\Ookla\ProcessSpeedtestBatch;
use App\Models\Result;
use App\Models\Schedule;
use Lorisleiva\Actions\Concerns\AsAction;

class StartSpeedtest
{
    use AsAction;

    public function handle(bool $scheduled = false, ?Schedule $schedule = null, array $scheduleOptions = [], ?int $serverId = null, bool $retry = false): void
    {
        $result = Result::create([
            'schedule_id' => $schedule?->id,
            'data->server->id' => $serverId,
            'service' => ResultService::Ookla,
            'status' => ResultStatus::Started,
            'scheduled' => $scheduled,
            'retry' => $retry,
        ]);

        // Dispatch the job to handle the server selection and speedtest process
        ProcessSpeedtestBatch::dispatch(
            $result,
            $scheduleOptions,
        );

        // Fire event that the speedtest has started
        SpeedtestStarted::dispatch($result);
    }
}
