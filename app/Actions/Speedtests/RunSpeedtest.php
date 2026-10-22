<?php

namespace App\Actions\Speedtests;

use App\Actions\Ookla\RunSpeedtest as RunOoklaSpeedtest;
use App\Enums\ResultService;
use App\Enums\ResultStatus;
use App\Events\SpeedtestWaiting;
use App\Jobs\CheckForInternetConnectionJob;
use App\Jobs\Ookla\BenchmarkSpeedtestJob;
use App\Jobs\Ookla\CompleteSpeedtestJob;
use App\Jobs\Ookla\SkipSpeedtestJob;
use App\Jobs\Ookla\StartSpeedtestJob;
use App\Jobs\Speedtests\RunSpeedtestJob;
use App\Models\Result;
use Illuminate\Bus\Batch;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class RunSpeedtest
{
    use AsAction;

    /**
     * Queue a speedtest with the given service. The target is the Ookla server ID,
     * the iPerf3 server or the file URL, depending on the service.
     */
    public function handle(
        ResultService $service = ResultService::Ookla,
        bool $scheduled = false,
        ?string $target = null,
        ?int $dispatchedBy = null,
    ): Result {
        if ($service === ResultService::Ookla) {
            return RunOoklaSpeedtest::run(
                scheduled: $scheduled,
                serverId: filled($target) ? (int) $target : null,
                dispatchedBy: $dispatchedBy,
            );
        }

        if (blank($target) && $service->requiresTarget() && filled($service->configuredTargets())) {
            $target = Arr::random($service->configuredTargets());
        }

        $result = Result::create([
            'data->server->host' => filled($target) ? trim($target) : null,
            'service' => $service,
            'status' => ResultStatus::Waiting,
            'scheduled' => $scheduled,
            'dispatched_by' => $dispatchedBy,
        ]);

        SpeedtestWaiting::dispatch($result);

        Bus::batch([
            [
                new StartSpeedtestJob($result),
                new CheckForInternetConnectionJob($result),
                new SkipSpeedtestJob($result),
                new RunSpeedtestJob($result),
                new BenchmarkSpeedtestJob($result),
                new CompleteSpeedtestJob($result),
            ],
        ])->catch(function (Batch $batch, ?Throwable $e) {
            Log::error(sprintf('Speedtest batch "%s" failed for an unknown reason.', $batch->id));
        })->name($service->getLabel().' Speedtest')->dispatch();

        return $result;
    }
}
