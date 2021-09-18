<?php

use App\Http\Controllers\AuthController;
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

// ====================================================
// Auth routes
// ====================================================

Route::post('login', [AuthController::class, 'login'])
    ->middleware('guest')
    ->name('login');
Route::get('profile', [AuthController::class, 'readProfile'])
    ->middleware('auth')
    ->name('profile');
Route::post('logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
