<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MetricsController;
use App\Http\Middleware\PrometheusAllowedIpMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', HomeController::class)
    ->middleware(['getting-started', 'public-dashboard'])
    ->name('home');

Route::get('/prometheus', MetricsController::class)
    ->middleware(PrometheusAllowedIpMiddleware::class)
    ->name('prometheus');

Route::view('/getting-started', 'getting-started')
    ->name('getting-started');

Route::redirect('/login', '/admin/login')
    ->name('login');

if (app()->isLocal()) {
    require __DIR__.'/test.php';
}
