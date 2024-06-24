<?php

use App\Http\Controllers\HomeController;
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

Route::middleware('public-dashboard')->group(function () {
    Route::get('/', HomeController::class)
        ->name('home');
});

Route::redirect('/login', '/admin/login')
    ->name('login');

if (app()->isLocal()) {
    require __DIR__.'/test.php';
}
