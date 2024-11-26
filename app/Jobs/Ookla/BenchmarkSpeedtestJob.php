<?php

namespace App\Jobs\Ookla;

use App\Enums\ResultStatus;
use App\Events\SpeedtestBenchmarking;
use App\Helpers\Benchmark;
use App\Models\Result;
use App\Settings\ThresholdSettings;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Arr;

class BenchmarkSpeedtestJob implements ShouldQueue
{
    use Batchable, Queueable;

    public bool $healthy = true;

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
        $settings = app(ThresholdSettings::class);

        if ($this->batch()->cancelled() || $settings->absolute_enabled == false) {
            return;
        }

        $this->result->update([
            'status' => ResultStatus::Benchmarking,
        ]);

        SpeedtestBenchmarking::dispatch($this->result);

        $benchmarks = $this->benchmark(
            result: $this->result,
            settings: $settings,
        );

        if (! count($benchmarks)) {
            return;
        }

        $this->result->update([
            'benchmarks' => $benchmarks,
            'healthy' => $this->healthy,
        ]);
    }

    private function benchmark(Result $result, ThresholdSettings $settings): array
    {
        $benchmarks = [];

        if (! blank($settings->absolute_download) && $settings->absolute_download > 0) {
            $benchmarks = Arr::add($benchmarks, 'download', [
                'bar' => 'min',
                'passed' => Benchmark::bitrate($result->download, ['value' => $settings->absolute_download, 'unit' => 'mbps']),
                'type' => 'absolute',
                'value' => $settings->absolute_download,
                'unit' => 'mbps',
            ]);

            if (Arr::get($benchmarks, 'download.passed') == false) {
                $this->healthy = false;
            }
        }

        if (! blank($settings->absolute_upload) && $settings->absolute_upload > 0) {
            $benchmarks = Arr::add($benchmarks, 'upload', [
                'bar' => 'min',
                'passed' => filter_var(Benchmark::bitrate($result->upload, ['value' => $settings->absolute_upload, 'unit' => 'mbps']), FILTER_VALIDATE_BOOLEAN),
                'type' => 'absolute',
                'value' => $settings->absolute_upload,
                'unit' => 'mbps',
            ]);

            if (Arr::get($benchmarks, 'upload.passed') == false) {
                $this->healthy = false;
            }
        }

        if (! blank($settings->absolute_ping) && $settings->absolute_ping > 0) {
            $benchmarks = Arr::add($benchmarks, 'ping', [
                'bar' => 'max',
                'passed' => Benchmark::ping($result->ping, ['value' => $settings->absolute_ping]),
                'type' => 'absolute',
                'value' => $settings->absolute_ping,
                'unit' => 'ms',
            ]);

            if (! Arr::get($benchmarks, 'ping.passed') == false) {
                $this->healthy = false;
            }
        }

        return $benchmarks;
    }
}
