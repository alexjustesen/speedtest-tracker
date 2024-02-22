<?php

use App\Http\Controllers\API\HealthCheckController;
use App\Http\Controllers\API\Speedtest\GetLatestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/healthcheck', HealthCheckController::class);

/**
 * This route provides backwards compatibility from https://github.com/henrywhitaker3/Speedtest-Tracker
 * for Homepage and Organizr dashboards which expects the returned
 * download and upload values in mbits.
 */
Route::get('/speedtest/latest', GetLatestController::class)
    ->name('speedtest.latest');
