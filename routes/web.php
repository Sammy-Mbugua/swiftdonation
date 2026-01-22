<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckRolePermission;


Route::get('/', [App\Http\Controllers\Front\HomeController::class, 'index']);
// Route::get('/', function () {
//     return view('welcome');
// });

// TODO: VORMIA ROUTES
Route::group(['prefix' => 'vrm'], function () {
// todo: login - admin
Route::controller(App\Http\Controllers\Admin\LoginController::class)->group(function () {
    Route::get('/admin', 'index')->name('/vrm/admin');
    Route::post('/admin/access', 'login');
    Route::get('/admin/logout', 'logout')->name('/vrm/admin/logout');
});

Route::middleware([CheckRolePermission::class . ':permissions'])->group(function () {

    Route::middleware([CheckRolePermission::class . ':users'])->group(function () {
        // ? Users
        Route::controller(App\Http\Controllers\Admin\UserController::class)->group(function () {
            Route::get('/users', 'index');
            Route::post('/users/save', 'store');
            Route::post('/users/update', 'update');
            Route::get('/users/edit/{page?}', 'edit'); // Edit
            Route::get('/users/delete', 'delete'); // Delete
            Route::get('/users/status/{action?}', 'valid'); // Valid
            Route::get('/users/{view}', 'open'); // Open
        });
    });

    // ? Roles
    Route::controller(App\Http\Controllers\Admin\RoleController::class)->group(function () {
        Route::get('/roles', 'index');
        Route::post('/roles/save', 'store');
        Route::post('/roles/update', 'update');
        Route::get('/roles/edit/{page?}', 'edit');
        Route::get('/roles/delete', 'delete');
        Route::get('/roles/{action}', 'valid');
    });
});

// Protect a group of routes
Route::middleware([CheckRolePermission::class . ':dashboard'])->group(function () {
    // ? Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('vrm/dashboard')->middleware(CheckRolePermission::class . ':dashboard');;
});

// Protect a group of routes
Route::middleware([CheckRolePermission::class . ':setup'])->group(function () {
    // ? Setup
    Route::group(['prefix' => 'setup'], function () {
        // ? Continent Hierarchies
        Route::controller(App\Http\Controllers\Setup\ContinentController::class)->group(function () {
            Route::get('/continent', 'index');
            Route::post('/continent/save', 'store');
            Route::post('/continent/update', 'update');
            Route::get('/continent/edit/{page?}', 'edit');
            Route::get('/continent/delete', 'delete');
            Route::get('/continent/{action}', 'valid');
        });

        // ? Currency
        Route::controller(App\Http\Controllers\Setup\CurrencyController::class)->group(function () {
            Route::get('/currency', 'index');
            Route::post('/currency/save', 'store');
            Route::post('/currency/update', 'update');
            Route::get('/currency/edit/{page?}', 'edit'); // Edit
            Route::get('/currency/delete', 'delete'); // Delete
            Route::get('/currency/status/{action?}', 'valid'); // Valid
            Route::get('/currency/{view}', 'open'); // Open
        });

        Route::controller(App\Http\Controllers\Setup\SubstriptionController::class)->group(function (){
            Route::get('/subscription', 'index');
            Route::post('/subscription/save', 'store');
            Route::post('/subscription/update', 'update');
            Route::get('/subscription/edit/{page?}', 'edit'); // Edit
            Route::get('/subscription/delete', 'delete'); // Delete
            Route::get('/subscription/status/{action?}', 'valid'); // Valid
            Route::get('/subscription/{view}', 'open'); // Open
        });


        // manage subscription
        Route::controller(App\Http\Controllers\Setup\Manage\SubscriptionController::class)->group(function (){
            Route::get('/manage-subscription', 'index');
            Route::post('/manage-subscription/save', 'store');
            Route::post('/manage-subscription/update', 'update');
            Route::get('/manage-subscription/edit/{page?}', 'edit'); // Edit
            Route::get('/manage-subscription/delete', 'delete'); // Delete
            Route::get('/manage-subscription/status/{action?}', 'valid'); // Valid
            Route::get('/manage-subscription/{view}', 'open'); // Open
        });
    });
});
});


// TODO: VORMIA LIVEWIRE
// Route::get('/', App\Livewire\LiveSetting::class)->name('home');


Route::controller(App\Http\Controllers\Portal\DashboardController::class)->group(function () {
    Route::get('/account/home/dashboard', 'index')->name('/public/profile')->middleware(CheckRolePermission::class . ':portal');
    Route::get('/logout', 'logout')->name('/public/profile');
    Route::get('/subs/{view}', 'open'); // Open
});


Route::controller(App\Http\Controllers\Portal\ReferralsController::class)->group(function () {
    Route::get('/portal/refarrals', 'index')->middleware(CheckRolePermission::class . ':portal');
    Route::get('/donate','donate')->middleware(CheckRolePermission::class . ':portal');
    Route::post('/subscribe', 'subscribe');


    Route::get('/pay_load/{OrderCode}', 'pay_load');
    Route::get('/payment/confirm', 'check_status');
});

Route::controller(App\Http\Controllers\Profile\CreatorProfileController::class)->group(function () {
    Route::get('//account/dashboard', 'index')->middleware(CheckRolePermission::class . ':portal');
    Route::get('/account/profile/edit', 'profileEdit')->middleware(CheckRolePermission::class . ':portal');
    Route::post('/account/profile/update', 'profileUpdate')->middleware(CheckRolePermission::class . ':portal'); // Edit
});

// Route::controller(App\Http\Controllers\Portal\PaymentController::class)->group(function () {
//     Route::get('/payment', 'index')->middleware(CheckRolePermission::class . ':portal');
//     Route::post('/payment/confirm', 'confirm')->middleware(CheckRolePermission::class . ':portal');
//     Route::get('/payment/verify', 'verify')->middleware(CheckRolePermission::class . ':portal');
//     Route::get('/payment/callback', 'callback')->middleware(CheckRolePermission::class . ':portal');
// });

// TODO: PROFILE
Route::controller(App\Http\Controllers\Portal\WithdrawalController::class)->group(function () {
    Route::get('/withdrawal', 'index')->middleware(CheckRolePermission::class . ':portal');
    Route::post('/withdrawal-fund', 'withdrawal')->middleware(CheckRolePermission::class . ':portal');
    Route::get('/withdrawal/verify', 'verify')->middleware(CheckRolePermission::class . ':portal');
    Route::get('/withdrawal/callback', 'callback')->middleware(CheckRolePermission::class . ':portal');   
});

Route::controller(App\Http\Controllers\Front\AuthController::class)->group(function () {
    Route::get('/login', 'index')->name('account-signin');
    Route::get('/register', 'registerview');
    Route::get('/two_step_auth', 'twoStepAuth');
    Route::post('/account-signin/access', 'login');
    Route::post('/account-signup/access', 'register');
    Route::get('/account-verification', 'verification');
    Route::get('/resetpassword', 'resetPassword');
    Route::post('/account/resetpassword', 'validatEmail');
    Route::get('/account/updatepassword', 'updatepasswordview');
    Route::post('/account/resetpassword/update', 'updatePassword');

    Route::post('/account/resend-verification', 'resendVerification')->name('resend.verification');


    Route::get('/logout', 'logout')->name('/public/profile');
});
