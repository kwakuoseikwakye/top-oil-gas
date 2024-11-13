<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RouteController;
use App\View\Components\CustomerComponent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [RouteController::class, 'dashboard'])->name('dashboard');
    Route::get('/customers', [RouteController::class, 'customers'])->name('customers');
    Route::get('/orders', [RouteController::class, 'orders'])->name('orders');
});

require __DIR__.'/auth.php';
