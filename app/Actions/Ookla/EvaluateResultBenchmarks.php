<?php

namespace App\Actions\Ookla;

use App\Helpers\Bitrate;
use App\Models\Result;
use Illuminate\Support\Arr;
use Lorisleiva\Actions\Concerns\AsAction;

class EvaluateResultBenchmarks
{
    use AsAction;

    public bool $healthy = true;

    public function handle(Result $result, array $benchmarks): bool
    {
        if (Arr::get($benchmarks, 'download', false)) {
            $this->checkBitrate($result->download, $benchmarks['download']);
        }

        if (Arr::get($benchmarks, 'upload', false)) {
            $this->checkBitrate($result->upload, $benchmarks['upload']);
        }

        if (Arr::get($benchmarks, 'ping', false)) {
            $this->checkPing($result->ping, $benchmarks['ping']);
        }

        return $this->healthy;
    }

    private function checkBitrate(float|int $bytes, array $benchmark): void
    {
        $value = Arr::get($benchmark, 'value');

        $unit = Arr::get($benchmark, 'unit');

        if (blank($value) || blank($unit)) {
            return;
        }

        if (Bitrate::bytesToBits($bytes) < Bitrate::normalizeToBits($value.$unit)) {
            $this->healthy = false;
        }
    }

    private function checkPing(float|int $ping, array $benchmark): void
    {
        $value = Arr::get($benchmark, 'value');

        if (blank($value)) {
            return;
        }

        if ($ping > $value) {
            $this->healthy = false;
        }
    }
}
