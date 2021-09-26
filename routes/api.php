<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RechargedCardController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
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

Route::post('register', [AuthController::class, 'register'])
    ->middleware(['guest'])
    ->name('register');

Route::post('login', [AuthController::class, 'login'])
    ->middleware(['guest'])
    ->name('login');

Route::get('profile', [AuthController::class, 'readProfile'])
    ->middleware(['auth'])
    ->name('profile');

Route::post('logout', [AuthController::class, 'logout'])
    ->middleware(['auth'])
    ->name('logout');

Route::patch('update-profile', [AuthController::class, 'updateProfile'])
    ->middleware(['auth'])
    ->name('profile.update');

Route::patch('update-password', [AuthController::class, 'updatePassword'])
    ->middleware(['auth'])
    ->name('password.update');

Route::post('forgot-password', [AuthController::class, 'forgotPassword'])
    ->middleware(['guest'])
    ->name('password.email');

Route::post('reset-password', [AuthController::class, 'resetPassword'])
    ->middleware(['guest'])
    ->name('password.reset');

// ====================================================
// Setting routes
// ====================================================

Route::prefix('settings')->group(function () {
    Route::get('public', [SettingController::class, 'getPublicSettings'])
        ->name('settings.public');
});


// ====================================================
// User routes
// ====================================================

Route::prefix('users')->group(function () {
    Route::get('find-strictly', [UserController::class, 'findStrictly'])
        ->name('users.findStrictly');
});


// ====================================================
// Recharged card routes
// ====================================================

Route::prefix('recharged-cards')->group(function () {
    Route::post('recharge', [RechargedCardController::class, 'recharge'])
        ->middleware(['auth', 'verified'])
        ->name('rechargedCards.recharge');

    Route::prefix('{rechargedCard}')->group(function () {
        Route::patch('start-approving', [RechargedCardController::class, 'startApproving'])
            ->middleware(['auth', 'verified', 'can:startApproving,rechargedCard'])
            ->name('rechargedCards.startApproving');
        Route::patch('end-approving', [RechargedCardController::class, 'endApproving'])
            ->middleware(['auth', 'verified', 'can:endApproving,rechargedCard'])
            ->name('rechargedCards.endApproving');
    });
});
