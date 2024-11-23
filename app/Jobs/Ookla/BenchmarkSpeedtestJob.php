<?php

namespace App\Jobs\Ookla;

use App\Actions\Ookla\EvaluateResultBenchmarks;
use App\Enums\ResultStatus;
use App\Events\SpeedtestBenchmarking;
use App\Models\Result;
use App\Settings\ThresholdSettings;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Arr;

class BenchmarkSpeedtestJob implements ShouldQueue
{
    use Batchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Result $result,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        $settings = app(ThresholdSettings::class);

        if ($settings->absolute_enabled === false) {
            return;
        }

        $this->result->update([
            'status' => ResultStatus::Benchmarking,
        ]);

        SpeedtestBenchmarking::dispatch($this->result);

        $benchmarks = $this->buildBenchmarks($settings);

        if (! count($benchmarks)) {
            return;
        }

        $this->result->update([
            'benchmarks' => $benchmarks,
            'healthy' => EvaluateResultBenchmarks::run($this->result, $benchmarks),
        ]);
    }

    private function buildBenchmarks(ThresholdSettings $settings): array
    {
        $benchmarks = [];

        if (! blank($settings->absolute_download) && $settings->absolute_download > 0) {
            $benchmarks = Arr::add($benchmarks, 'download', [
                'bar' => 'min',
                'type' => 'absolute',
                'value' => $settings->absolute_download,
                'unit' => 'mbps',
            ]);
        }

        if (! blank($settings->absolute_upload) && $settings->absolute_upload > 0) {
            $benchmarks = Arr::add($benchmarks, 'upload', [
                'bar' => 'min',
                'type' => 'absolute',
                'value' => $settings->absolute_upload,
                'unit' => 'mbps',
            ]);
        }

        if (! blank($settings->absolute_ping) && $settings->absolute_ping > 0) {
            $benchmarks = Arr::add($benchmarks, 'ping', [
                'bar' => 'max',
                'type' => 'absolute',
                'value' => $settings->absolute_ping,
                'unit' => 'ms',
            ]);
        }

        return $benchmarks;
    }
}
