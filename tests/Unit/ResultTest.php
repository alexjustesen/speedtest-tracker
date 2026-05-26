<?php

use App\Enums\ResultStatus;
use App\Models\Result;

it('scopes results to only completed status', function () {
    Result::factory()->create(['status' => ResultStatus::Completed]);
    Result::factory()->create(['status' => ResultStatus::Completed]);
    Result::factory()->create(['status' => ResultStatus::Failed]);
    Result::factory()->create(['status' => ResultStatus::Running]);

    $completedResults = Result::completed()->get();

    expect($completedResults)->toHaveCount(2);
    expect($completedResults->every(fn ($result) => $result->status === ResultStatus::Completed))->toBeTrue();
});
