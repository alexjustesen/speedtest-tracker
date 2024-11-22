<?php

namespace App\Actions\Ookla;

use App\Enums\ResultService;
use App\Enums\ResultStatus;
use App\Events\SpeedtestStarted;
use App\Jobs\Ookla\ProcessSpeedtestBatch;
use App\Models\Result;
use Lorisleiva\Actions\Concerns\AsAction;

class StartSpeedtest
{
    use AsAction;

    public function handle(bool $scheduled = false, ?int $serverId): void
    {
        $result = Result::create([
            'service' => ResultService::Ookla,
            'status' => ResultStatus::Started,
            'scheduled' => $scheduled,
        ]);

        if (blank($serverId)) {
            $serverId = SelectSpeedtestServer::run();
        }


        if (! blank($serverId)) {
            $result->update([
                'data->server->id' => $serverId,
            ]);
        }

        ProcessSpeedtestBatch::dispatch(
            result: $result,
        );

        SpeedtestStarted::dispatch($result);
    }
}
