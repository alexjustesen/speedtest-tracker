<?php

namespace App\Actions\Ookla;

use App\Enums\ResultService;
use App\Enums\ResultStatus;
use App\Events\SpeedtestWaiting;
use App\Jobs\CheckForInternetConnectionJob;
use App\Jobs\Ookla\BenchmarkSpeedtestJob;
use App\Jobs\Ookla\CompleteSpeedtestJob;
use App\Jobs\Ookla\RunSpeedtestJob;
use App\Jobs\Ookla\SelectSpeedtestServerJob;
use App\Jobs\Ookla\SkipSpeedtestJob;
use App\Jobs\Ookla\StartSpeedtestJob;
use App\Models\Result;
use App\Models\Schedule;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class RunSpeedtest
{
    use AsAction;

    public function handle(bool $scheduled = false, ?Schedule $schedule = null, ?int $serverId = null, array $scheduleOptions = []): mixed
    {
        $result = Result::create([
            'schedule_id' => $schedule?->id,
            'data->server->id' => $serverId,
            'service' => ResultService::Ookla,
            'status' => ResultStatus::Waiting,
            'scheduled' => $scheduled,
        ]);

        SpeedtestWaiting::dispatch($result);

        $skipIps = $scheduleOptions['skip_ips'] ?? [];
        $interface = $scheduleOptions['interface'] ?? null;

        Bus::batch([
            [
                new StartSpeedtestJob($result),
                new CheckForInternetConnectionJob($result),
                new SkipSpeedtestJob($result, $skipIps),
                new SelectSpeedtestServerJob($result),
                new RunSpeedtestJob($result, $interface),
                new BenchmarkSpeedtestJob($result),
                new CompleteSpeedtestJob($result),
            ],
        ])->catch(function (Batch $batch, ?Throwable $e) {
            Log::error(sprintf('Speedtest batch "%s" failed for an unknown reason.', $batch->id));
        })->name('Ookla Speedtest')->dispatch();

        return $result;
    }
}
