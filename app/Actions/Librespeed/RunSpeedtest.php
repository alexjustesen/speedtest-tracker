<?php

namespace App\Actions\Librespeed;

use App\Enums\ResultService;
use App\Enums\ResultStatus;
use App\Events\SpeedtestWaiting;
use App\Models\Result;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class RunSpeedtest
{
    use AsAction;

    public function handle(bool $isScheduled = false, ?string $server = null, ?int $dispatchedBy = null): mixed
    {
        $result = Result::create([
            'data->server->url' => $server,
            'service' => ResultService::Librespeed,
            'status' => ResultStatus::Waiting,
            'scheduled' => $isScheduled,
            'dispatched_by' => $dispatchedBy,
        ]);

        SpeedtestWaiting::dispatch($result);

        // TODO: Implement Librespeed speedtest job batching

        // Bus::batch([
        //     [
        //         new StartSpeedtestJob($result),
        //         new CheckForInternetConnectionJob($result),
        //         new SkipSpeedtestJob($result),
        //         new SelectSpeedtestServerJob($result),
        //         new RunSpeedtestJob($result),
        //         new BenchmarkSpeedtestJob($result),
        //         new CompleteSpeedtestJob($result),
        //     ],
        // ])->catch(function (Batch $batch, ?Throwable $e) {
        //     Log::error(sprintf('Speedtest batch "%s" failed for an unknown reason.', $batch->id));
        // })->name('Ookla Speedtest')->dispatch();

        return $result;
    }
}
