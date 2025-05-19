<?php

use App\Http\Controllers\Api\V1\LatestResult;
use App\Http\Controllers\Api\V1\ListResults;
use App\Http\Controllers\Api\V1\RunSpeedtest;
use App\Http\Controllers\Api\V1\ShowResult;
use App\Http\Controllers\Api\V1\Stats;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/results', ListResults::class)
        ->name('results.list');

    Route::get('/results/latest', LatestResult::class)
        ->name('results.latest');

    Route::get('/results/{result}', ShowResult::class)
        ->name('results.show');

    Route::get('/speedtests/run', RunSpeedtest::class)
        ->name('speedtests.run');

    Route::get('/stats', Stats::class)
        ->name('stats');
});
