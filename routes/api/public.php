<?php

use App\Http\Controllers\Api\Public\ChartDataController;
use App\Http\Controllers\Api\Public\ServersController;
use App\Http\Controllers\Api\Public\StatisticsController;
use App\Http\Controllers\Api\Public\StatsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
|
| These routes are publicly accessible without authentication.
| Used by Dashboard V2 for fetching stats and server information.
|
*/

Route::get('/stats', StatsController::class);
Route::get('/servers', ServersController::class);
Route::get('/statistics/{metric}', StatisticsController::class);
Route::get('/chart-data/{metric}', ChartDataController::class);
