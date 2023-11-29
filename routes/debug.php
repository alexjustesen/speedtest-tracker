<?php

use App\Livewire\Debug\Timezone as DebugTimezone;
use Illuminate\Support\Facades\Route;

Route::get('/debug/timezone', DebugTimezone::class)
    ->name('debug.timezone');
