<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Todo: API VERSION 1
Route::group(['prefix' =>  'v1'], function () {
    // Todo: v1 Auth
    Route::group(['prefix' => '/auth'], function () {
        Route::group(['prefix' => '/register'], function () {
            Route::controller(App\Http\Controllers\Api\V1\Auth\Registration::class)->group(function () {
                Route::post('/user', 'user_registration');
            });
        });
    });


    // Todo: v1 Work
    // Route::group(['prefix' => '/work'], function () {
    //     Route::group(['prefix' => '/profile'], function () {
    //         Route::controller(App\Http\Controllers\Api\V1\Work\Profile::class)->group(function () {
    //             Route::post('/', 'manage_work_profile');
    //             Route::get('/get', 'get_work_profile');
    //         });
    //     });
    // });

    // Todo: v1 Manage
    Route::group(['prefix' => '/manage'], function () {
        // Todo: v1 Manage Package
        Route::group(['prefix' => '/package'], function () {
            Route::controller(App\Http\Controllers\Api\V1\Manage\Package::class)->group(function () {
                Route::post('/', 'manage_package')->name('makepackage');
                Route::get('/get', 'get_package');
            });
        });

        // Todo: v1 Manage Billing
        Route::group(['prefix' => '/billing'], function () {
            Route::controller(App\Http\Controllers\Api\V1\Manage\Billing::class)->group(function () {
                Route::post('/', 'issue_billing');
                Route::get('/get', 'get_billing');
            });
        });

        // Todo: v1 Manage Payment
        Route::group(['prefix' => '/payment'], function () {
            Route::controller(App\Http\Controllers\Api\V1\Payment\ManagePayment::class)->group(function () {
                Route::post('/pay-now', 'make_payment');
                Route::get('/pay-retry', 'make_payment_retry');
            });

            // Callack
            Route::controller(App\Http\Controllers\Api\V1\Payment\ChpterGateway::class)->group(function () {
                Route::post('/callback', 'callback');
                Route::get('/get-callback', 'callback');
            });
        });
    });

    /**
 * Todo : Chapter Payment
 */
Route::controller(App\Http\Controllers\Api\Payment\ChpterGateway::class)->group(function () {
    Route::post('/chpter-callback', 'callback')->name('chpter-callback');
    Route::get('/get-chpter-callback', 'callback')->name('get-chpter-callback');
});

    /**
 * Todo : Mpesa Payment
 */
Route::controller(App\Http\Controllers\Api\Payment\MpesaGateway::class)->group(function () {
    Route::post('/mpesa-callback', 'callback')->name('mpesa-callback');
    Route::get('/payment/callback', 'callback')->name('get-mpesa-callback');
});

});

