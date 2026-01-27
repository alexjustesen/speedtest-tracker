<?php

use Illuminate\Support\Facades\Route;

/**
 * Health check route to ensure the API is up and running.
 */
Route::get('/healthcheck', function () {
    return response()->json([
        'message' => 'Speedtest Tracker is running!',
    ]);
})->name('healthcheck');

Route::middleware(['auth:sanctum', 'throttle:api', 'accept-json'])->group(function () {
    require __DIR__.'/api/v1/routes.php';
});
