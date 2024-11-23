<?php

namespace App\Actions\Ookla;

use App\Helpers\Benchmark;
use App\Models\Result;
use Illuminate\Support\Arr;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * TODO: refactored after Sven merges benchmark passed indicator.
 */
class EvaluateResultHealth
{
    use AsAction;

    public bool $healthy = true;

    public function handle(Result $result, array $benchmarks): bool
    {
        if (Arr::get($benchmarks, 'download', false) && ! Benchmark::bitrate($result->download, $benchmarks['download']) ) {
            $this->healthy = false;
        }

        if (Arr::get($benchmarks, 'upload', false) && ! Benchmark::bitrate($result->upload, $benchmarks['upload'])) {
            $this->healthy = false;
        }

        if (Arr::get($benchmarks, 'ping', false) && ! Benchmark::ping($result->ping, $benchmarks['ping'])) {
            $this->healthy = false;
        }

        return $this->healthy;
    }
}
