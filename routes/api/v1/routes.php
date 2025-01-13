<?php

use App\Http\Controllers\Api\V1\LatestResult;
use App\Http\Controllers\Api\V1\ListResults;
use App\Http\Controllers\Api\V1\ShowResult;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/results', ListResults::class)
        // ->middleware() // TODO: protect the route with a gate referencing the authenticated user's role.
        ->name('results.list');

    Route::get('/results/latest', LatestResult::class)
        // ->middleware() // TODO: protect the route with a gate referencing the authenticated user's role.
        ->name('results.latest');

    Route::get('/results/{result}', ShowResult::class)
        // ->middleware() // TODO: protect the route with a gate referencing the authenticated user's role.
        ->name('results.show');
});
