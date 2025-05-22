<?php

use App\Http\Controllers\Api\V1\LatestResult;
use App\Http\Controllers\Api\V1\ListResults;
use App\Http\Controllers\Api\V1\ListSpeedtestServers;
use App\Http\Controllers\Api\V1\RunSpeedtest;
use App\Http\Controllers\Api\V1\ShowResult;
use App\Http\Controllers\Api\V1\Stats;
use App\Http\Controllers\Api\V1\TokenController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/results', ListResults::class)
        ->name('results.list');

    Route::get('/results/latest', LatestResult::class)
        ->name('results.latest');

    Route::get('/results/{result}', ShowResult::class)
        ->name('results.show');

    Route::post('/speedtests/run', RunSpeedtest::class)
        ->name('speedtests.run');

    Route::get('/ookla/list-servers', ListSpeedtestServers::class)
        ->name('ookla.list-servers');

    Route::post('/app/tokens', [TokenController::class, 'store'])
        ->name('app.tokens.store');

    Route::post('/app/tokens/{id}/edit', [TokenController::class, 'update'])
        ->name('app.tokens.updateScopes');

    Route::get('/app/tokens', [TokenController::class, 'index'])
        ->name('app.tokens.index');

    Route::delete('/app/tokens/{id}', [TokenController::class, 'destroy'])
        ->name('app.tokens.destroy');

    Route::get('/stats', Stats::class)
        ->name('stats');
});
