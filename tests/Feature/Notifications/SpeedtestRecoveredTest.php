<?php

use App\Enums\ResultStatus;
use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestRecovered;
use App\Jobs\Ookla\CompleteSpeedtestJob;
use App\Models\Result;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake([SpeedtestCompleted::class, SpeedtestRecovered::class]);
});

function completeSpeedtest(): Result
{
    $result = Result::factory()->create(['status' => ResultStatus::Running]);

    (new CompleteSpeedtestJob($result))->handle();

    return $result;
}

it('fires a recovered event when a speedtest completes after a failed one', function () {
    Result::factory()->create(['status' => ResultStatus::Failed]);

    completeSpeedtest();

    Event::assertDispatched(SpeedtestCompleted::class);
    Event::assertDispatched(SpeedtestRecovered::class);
});

it('ignores skipped speedtests when looking for the previous one', function () {
    Result::factory()->create(['status' => ResultStatus::Failed]);
    Result::factory()->create(['status' => ResultStatus::Skipped]);

    completeSpeedtest();

    Event::assertDispatched(SpeedtestRecovered::class);
});

it('does not fire a recovered event when the previous speedtest completed', function () {
    Result::factory()->create(['status' => ResultStatus::Completed]);

    completeSpeedtest();

    Event::assertNotDispatched(SpeedtestRecovered::class);
});

it('does not fire a recovered event for the first speedtest', function () {
    completeSpeedtest();

    Event::assertNotDispatched(SpeedtestRecovered::class);
});
