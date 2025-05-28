<?php

use App\Http\Controllers\Api\V1\ResultsController;
use App\Http\Controllers\Api\V1\SpeedtestController;
use App\Http\Controllers\Api\V1\StatsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/results', [ResultsController::class, 'index'])
        ->name('results.list');

    Route::get('/results/latest', [ResultsController::class, 'latest'])
        ->name('results.latest');

    Route::get('/results/{id}', [ResultsController::class, 'show'])
        ->name('results.show');

    Route::post('/speedtests/run', [SpeedtestController::class, 'run'])
        ->name('speedtests.run');

    Route::get('/speedtests/list-servers', [SpeedtestController::class, 'listServers'])
        ->name('speedtests.list-servers');

    Route::get('/stats', [StatsController::class, 'aggregated'])
        ->name('stats.aggregated');
});
