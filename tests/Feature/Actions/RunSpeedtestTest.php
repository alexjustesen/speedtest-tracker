<?php

use App\Actions\Ookla\RunSpeedtest;
use App\Jobs\CheckForInternetConnectionJob;
use App\Jobs\Ookla\BenchmarkSpeedtestJob;
use App\Jobs\Ookla\RunSpeedtestJob;
use App\Jobs\Ookla\SkipSpeedtestJob;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    Bus::fake();
});

describe('RunSpeedtest', function () {
    it('defaults new runs to attempt 1', function () {
        RunSpeedtest::run();

        Bus::assertBatched(function (PendingBatch $batch) {
            $jobs = $batch->jobs->flatten();

            return $jobs->first(fn ($job) => $job instanceof RunSpeedtestJob)?->attempt === 1
                && $jobs->first(fn ($job) => $job instanceof BenchmarkSpeedtestJob)?->attempt === 1
                && $jobs->first(fn ($job) => $job instanceof SkipSpeedtestJob)?->attempt === 1
                && $jobs->first(fn ($job) => $job instanceof CheckForInternetConnectionJob)?->attempt === 1;
        });
    });

    it('threads a specific attempt number into every job that can report failure', function () {
        RunSpeedtest::run(attempt: 3);

        Bus::assertBatched(function (PendingBatch $batch) {
            $jobs = $batch->jobs->flatten();

            return $jobs->first(fn ($job) => $job instanceof RunSpeedtestJob)?->attempt === 3
                && $jobs->first(fn ($job) => $job instanceof BenchmarkSpeedtestJob)?->attempt === 3
                && $jobs->first(fn ($job) => $job instanceof SkipSpeedtestJob)?->attempt === 3
                && $jobs->first(fn ($job) => $job instanceof CheckForInternetConnectionJob)?->attempt === 3;
        });
    });
});
