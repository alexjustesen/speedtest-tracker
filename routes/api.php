<?php

use App\Http\Controllers\API\Speedtest\MeasurementController;
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

Route::get('/speedtest/latest', [MeasurementController::class, 'getLatest'])
    ->name('speedtest.latest');

Route::post('/speedtest', [MeasurementController::class, 'createNew'])
    ->name('speedtest.create');

Route::get('speedtest/trackingid/{id}', [MeasurementController::class, 'getMeasurementByTrackingId'])
    ->name('speedtest.getbytrackingid');
