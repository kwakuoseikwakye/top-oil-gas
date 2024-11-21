<?php

use App\Http\Controllers\api\v2\AuthController;
use App\Http\Controllers\api\v2\CylinderController;
use App\Http\Controllers\api\v2\PaymentController;
use App\Http\Controllers\api\v2\UserController;
use App\Http\Controllers\api\v2\WarehouseController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CylinderController as ControllersCylinderController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'v2'], function () {
    Route::post("login", [AuthController::class, "login"]);
    Route::post("signup", [AuthController::class, "signUp"]);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('send-otp', [AuthController::class, 'sendOtp']);
    Route::post('forgot-password', [AuthController::class, 'passwordReset']);

    Route::get('/pickup-locations', [WarehouseController::class, 'getPickupLocations']);

    Route::prefix("cylinders")->group(function () {
        Route::get('/weight', [CylinderController::class, 'getWeight']);
    });
});

Route::group(['prefix' => 'v2', 'middleware' => 'auth:sanctum'], function () {
    Route::prefix("users")->group(function () {
        Route::patch('/change-password', [UserController::class, 'changePassword']);
        Route::get('/dispatch/{orderNumber}', [UserController::class, 'getDispatch']);

        Route::prefix("order")->group(function () {
            Route::post('/', [UserController::class, 'createOrder']);
            Route::get('/history', [UserController::class, 'getOrderHistory']);

        });

        Route::prefix("location")->group(function () {
            Route::post('/', [UserController::class, 'addLocation']);
            Route::get('/', [UserController::class, 'getLocation']);
            Route::patch('/{id}', [UserController::class, 'updateLocation']);
            Route::delete('/{id}', [UserController::class, 'deleteLocation']);
            Route::patch('/set-default/{id}', [UserController::class, 'setDefaultLocation']);
        });

        Route::prefix("upload")->group(function () {
            Route::post('/file', [UserController::class, 'uploadFile']);

        });
        
    });

    Route::prefix("payments")->group(function () {
        Route::post('/initiate', [PaymentController::class, "initiatePayment"]);
        Route::patch('/verify-payment/{transactionId}', [PaymentController::class, "verifyPayment"]);
    });

});



Route::group(['prefix' => 'admin'], function () {

    Route::prefix("customers")->group(function () {
        Route::get('/', [CustomerController::class, 'index']);
        Route::post('/assign-cylinder', [CustomerController::class, 'index']);
    });

    Route::prefix("orders")->group(function () {
        Route::get('/', [OrderController::class, 'index']);
    });

    Route::prefix("cylinders")->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/assign', [ControllersCylinderController::class, 'assignCylinder']);
    });

});
