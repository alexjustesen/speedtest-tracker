<?php

use App\Jobs\Ookla\RunSpeedtestJob;
use App\Models\Result;

describe('RunSpeedtestJob', function () {
    test('defaults to attempt 1 when not specified', function () {
        $result = Result::factory()->create();

        $job = new RunSpeedtestJob($result);

        expect($job->attempt)->toBe(1);
    });

    test('accepts and stores a specific attempt number', function () {
        $result = Result::factory()->create();

        $job = new RunSpeedtestJob($result, 3);

        expect($job->attempt)->toBe(3);
    });
});
