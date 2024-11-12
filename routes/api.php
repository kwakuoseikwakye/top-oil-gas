<?php

use App\Http\Controllers\api\v2\AuthController;
use App\Http\Controllers\api\v2\CylinderController;
use App\Http\Controllers\api\v2\WarehouseController;
use App\Http\Controllers\CustomerController;
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



Route::group(['prefix' => 'admin'], function () {

    Route::prefix("customers")->group(function () {
        Route::get('/', [CustomerController::class, 'index']);
    });

});
