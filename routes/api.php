<?php

use App\Http\Controllers\Api\V0\GetLatestController;
use Illuminate\Support\Facades\Route;

/**
 * Health check route to ensure the API is up and running.
 */
Route::get('/healthcheck', function () {
    return response()->json([
        'message' => 'Speedtest Tracker is running!',
    ]);
})->name('healthcheck');

/**
 * This route provides backwards compatibility from https://github.com/henrywhitaker3/Speedtest-Tracker
 * for Homepage and Organizr dashboards which expects the returned
 * download and upload values in mbits.
 *
 * @deprecated
 */
Route::get('/speedtest/latest', GetLatestController::class)
    ->middleware('accept-json')
    ->name('speedtest.latest');

Route::middleware(['auth:sanctum', 'throttle:api', 'accept-json'])->group(function () {
    require __DIR__.'/api/v1/routes.php';
});
