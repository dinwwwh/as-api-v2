<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountInfoController;
use App\Http\Controllers\AccountTypeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RechargedCardController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ThesieureController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ValidationController;
use App\Http\Controllers\ValidatorController;
use App\Models\Account;
use App\Models\AccountInfo;
use App\Models\AccountType;
use App\Models\Tag;
use App\Models\Validator;
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
    Route::get('', [SettingController::class, 'index'])
        ->name('settings.index');

    Route::get('public', [SettingController::class, 'getPublicSettings'])
        ->name('settings.public');

    Route::prefix('{setting}')->group(function () {
        Route::get('', [SettingController::class, 'show'])
            ->name('settings.show');
        Route::put('', [SettingController::class, 'update'])
            ->middleware(['auth', 'verified', 'can:update,setting'])
            ->name('settings.update');
    });
});


// ====================================================
// User routes
// ====================================================

Route::prefix('users')->group(function () {
    Route::get('', [UserController::class, 'index'])
        ->name('users.index');

    Route::get('find-strictly', [UserController::class, 'findStrictly'])
        ->name('users.findStrictly');

    Route::get('search-strictly', [UserController::class, 'searchStrictly'])
        ->name('users.searchStrictly');

    Route::prefix('{user}')->group(function () {
        Route::get('', [UserController::class, 'show'])
            ->name('users.show');
        Route::patch('balance', [UserController::class, 'updateBalance'])
            ->middleware(['auth', 'verified', 'can:update,user'])
            ->name('users.updateBalance');
    });
});

// ====================================================
// Tag routes
// ====================================================

Route::prefix('tags')->group(function () {
    Route::get('', [TagController::class, 'index'])
        ->name('tags.index');

    Route::post('', [TagController::class, 'create'])
        ->middleware(['auth', 'verified', 'can:create,' . Tag::class])
        ->name('tags.create');

    Route::prefix('{tag}')->group(function () {
        Route::get('', [TagController::class, 'show'])
            ->name('tags.show');
        Route::put('', [TagController::class, 'update'])
            ->middleware(['auth', 'verified', 'can:update,tag'])
            ->name('tags.update');
        Route::patch('migrate/{migratedTag}', [TagController::class, 'migrate'])
            ->middleware(['auth', 'verified', 'can:update,tag'])
            ->name('tags.migrate');
        Route::get('accounts/selling', [TagController::class, 'getSellingAccountsByTag'])
            ->name('accounts.getSellingAccountsByTag');
    });
});

// ====================================================
// Recharged card routes
// ====================================================

Route::prefix('recharged-cards')->group(function () {
    Route::post('recharge', [RechargedCardController::class, 'recharge'])
        ->middleware(['auth', 'verified'])
        ->name('rechargedCards.recharge');

    Route::get('', [RechargedCardController::class, 'index'])
        ->name('rechargedCards.index');

    Route::get('pending', [RechargedCardController::class, 'getPending'])
        ->name('rechargedCards.getPending');

    Route::get('approving', [RechargedCardController::class, 'getApproving'])
        ->name('rechargedCards.getApproving');

    Route::get('approving-by-me', [RechargedCardController::class, 'getApprovingByMe'])
        ->middleware(['auth', 'verified'])
        ->name('rechargedCards.getApprovingByMe');

    Route::prefix('{rechargedCard}')->group(function () {
        Route::get('', [RechargedCardController::class, 'show'])
            ->name('rechargedCards.show');
        Route::patch('start-approving', [RechargedCardController::class, 'startApproving'])
            ->middleware(['auth', 'verified', 'can:startApproving,rechargedCard'])
            ->name('rechargedCards.startApproving');
        Route::patch('end-approving', [RechargedCardController::class, 'endApproving'])
            ->middleware(['auth', 'verified', 'can:endApproving,rechargedCard'])
            ->name('rechargedCards.endApproving');
    });
});

// ====================================================
// Account type routes
// ====================================================

Route::prefix('account-types')->group(function () {
    Route::post('', [AccountTypeController::class, 'create'])
        ->middleware(['auth', 'verified', 'can:create,' . AccountType::class])
        ->name('accountTypes.create');

    Route::get('', [AccountTypeController::class, 'index'])
        ->name('accountTypes.index');

    Route::get('created-by-me', [AccountTypeController::class, 'getCreatedByMe'])
        ->middleware(['auth', 'verified'])
        ->name('accountTypes.getCreatedByMe');

    Route::get('usable', [AccountTypeController::class, 'usable'])
        ->middleware(['auth', 'verified'])
        ->name('accountTypes.usable');

    Route::prefix('{accountType}')->group(function () {
        Route::get('', [AccountTypeController::class, 'show'])
            ->name('accountTypes.show');

        Route::put('', [AccountTypeController::class, 'update'])
            ->middleware(['auth', 'verified', 'can:update,accountType'])
            ->name('accountTypes.update');

        Route::prefix('account-infos')->group(function () {
            Route::post('', [AccountInfoController::class, 'create'])
                ->middleware(['auth', 'verified', 'can:create,' . AccountInfo::class . ',accountType'])
                ->name('accountInfos.create');
        });

        Route::prefix('accounts')->group(function () {
            Route::post('', [AccountController::class, 'create'])
                ->middleware(['auth', 'verified', 'can:create,' . Account::class . ',accountType'])
                ->name('accounts.create');
        });

        Route::prefix('validatorables')->group(function () {
            Route::post('{validator}', [AccountTypeController::class, 'createValidatorable'])
                ->middleware(['auth', 'verified', 'can:createValidatorable,accountType,validator'])
                ->name('accountTypes.createValidatorable');
            Route::delete('{validatorable}', [AccountTypeController::class, 'deleteValidatorable'])
                ->middleware(['auth', 'verified', 'can:deleteValidatorable,accountType,validatorable'])
                ->name('accountTypes.deleteValidatorable');
        });
    });
});

// ====================================================
// Account type routes
// ====================================================

Route::prefix('account-infos')->group(function () {
    Route::prefix('{accountInfo}')->group(function () {
        Route::get('', [AccountInfoController::class, 'show'])
            ->name('accountInfos.show');
        Route::put('', [AccountInfoController::class, 'update'])
            ->middleware(['auth', 'verified', 'can:update,accountInfo'])
            ->name('accountInfos.update');;
    });
});

// ====================================================
// Account route
// ====================================================

Route::prefix('accounts')->group(function () {

    Route::get('', [AccountController::class, 'index'])
        ->name('accounts.index');

    Route::get('created-by-me', [AccountController::class, 'getCreatedByMe'])
        ->middleware(['auth', 'verified'])
        ->name('accounts.getCreatedByMe');

    Route::get('bought-by-me', [AccountController::class, 'getBoughtByMe'])
        ->middleware(['auth', 'verified'])
        ->name('accounts.getBoughtByMe');

    Route::prefix('{account}')->group(function () {
        Route::get('', [AccountController::class, 'show'])
            ->name('accounts.show');
        Route::put('', [AccountController::class, 'update'])
            ->middleware(['auth', 'verified', 'can:update,account'])
            ->name('accounts.update');
        Route::patch('buy', [AccountController::class, 'buy'])
            ->middleware(['auth', 'verified', 'can:buy,account'])
            ->name('accounts.buy');
        Route::patch('confirm', [AccountController::class, 'confirm'])
            ->middleware(['auth', 'verified', 'can:confirm,account'])
            ->name('accounts.confirm');
    });
});

// ====================================================
// Thesieure route
// ====================================================
Route::prefix('thesieure')->group(function () {
    Route::get('callback', [ThesieureController::class, 'callback'])
        ->name('thesieure.callback');
    Route::get('telcos', [ThesieureController::class, 'getTelcos'])
        ->name('thesieure.getTelcos');
});

// ====================================================
// Validator routes
// ====================================================
Route::prefix('validators')->group(function () {
    Route::get('', [ValidatorController::class, 'index'])
        ->name('validators.index');

    Route::post('', [ValidatorController::class, 'create'])
        ->middleware(['auth', 'verified', 'can:create,' . Validator::class])
        ->name('validators.create');

    Route::prefix('{validator}')->group(function () {
        Route::get('', [ValidatorController::class, 'show'])
            ->name('validators.show');
        Route::put('', [ValidatorController::class, 'update'])
            ->middleware(['auth', 'verified', 'can:update,validator'])
            ->name('validators.update');
    });
});

// ====================================================
// Validation routes
// ====================================================
Route::prefix('validations')->group(function () {
    Route::get('', [ValidationController::class, 'index'])
        ->name('validations.index');

    Route::get('approvable-by-me', [ValidationController::class, 'approvableByMe'])
        ->middleware(['auth', 'verified'])
        ->name('validations.approvableByMe');

    Route::prefix('{validation}')->group(function () {
        Route::get('', [ValidationController::class, 'show'])
            ->name('validations.show');
        Route::patch('start-approving', [ValidationController::class, 'startApproving'])
            ->middleware(['auth', 'verified', 'can:startApproving,validation'])
            ->name('validations.startApproving');
        Route::patch('end-approving', [ValidationController::class, 'endApproving'])
            ->middleware(['auth', 'verified', 'can:endApproving,validation'])
            ->name('validations.endApproving');
    });
});
