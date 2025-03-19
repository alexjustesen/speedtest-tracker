<?php

use App\Http\Controllers\PagesController;
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

Route::get('/', [PagesController::class, 'home'])
    ->middleware(['getting-started', 'public-dashboard'])
    ->name('home');

Route::get('/getting-started', [PagesController::class, 'gettingStarted'])
    ->name('getting-started');

Route::redirect('/login', '/admin/login')
    ->name('login');

if (app()->isLocal()) {
    require __DIR__.'/test.php';
}
