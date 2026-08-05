<?php

namespace App\Actions\Nettest;

use App\Enums\ResultService;
use App\Enums\ResultStatus;
use App\Events\SpeedtestWaiting;
use App\Jobs\CheckForInternetConnectionJob;
use App\Jobs\Nettest\CompleteSpeedtestJob;
use App\Jobs\Nettest\RunSpeedtestJob;
use App\Jobs\Nettest\StartSpeedtestJob;
use App\Models\Result;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class RunSpeedtest
{
    use AsAction;

    /**
     * Runs a speedtest with the Nettest CLI.
     *
     * The server is taken from the configuration because Nettest has no public
     * server list, so a server ID has no meaning here and is ignored.
     */
    public function handle(bool $scheduled = false, ?int $serverId = null, ?int $dispatchedBy = null): mixed
    {
        $result = Result::create([
            'service' => ResultService::Nettest,
            'status' => ResultStatus::Waiting,
            'scheduled' => $scheduled,
            'dispatched_by' => $dispatchedBy,
        ]);

        SpeedtestWaiting::dispatch($result);

        Bus::batch([
            [
                new StartSpeedtestJob($result),
                new CheckForInternetConnectionJob($result),
                new RunSpeedtestJob($result),
                new CompleteSpeedtestJob($result),
            ],
        ])->catch(function (Batch $batch, ?Throwable $e) {
            Log::error(sprintf('Speedtest batch "%s" failed for an unknown reason.', $batch->id));
        })->name('Nettest Speedtest')->dispatch();

        return $result;
    }
}
