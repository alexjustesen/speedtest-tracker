<?php

use App\Actions\MigrateBadJsonResults;
use Illuminate\Support\Facades\Route;

Route::prefix('test')->group(function () {
    // silence is golden

    Route::get('/', function () {
        // Result::truncate();

        MigrateBadJsonResults::dispatch();

        echo 'Job dispatched...';
    });
});
