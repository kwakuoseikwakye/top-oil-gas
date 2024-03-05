<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CylinderController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\OTPController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
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


Route::get('/', [RouteController::class, 'dashboard'])->name('dashboard');
Route::get('dispatch', [RouteController::class, 'vendors'])->name('dispatch');
Route::get('customers', [RouteController::class, 'customers'])->name('customers');
Route::get('employees', [RouteController::class, 'employees'])->name('employees');
Route::get('logs', [RouteController::class, 'logs'])->name('logs');
Route::get('users', [RouteController::class, 'users'])->name('admin users');
Route::get('cylinders', [RouteController::class, 'cylinders'])->name('cylinders');
Route::get('warehouse', [RouteController::class, 'warehouse'])->name('warehouse');
Route::get('reports/customers', [RouteController::class, 'customersReport'])->name('reports');
Route::get('reports/cylinders', [RouteController::class, 'cylinderReport'])->name('reports');
Route::get('reports/sales', [RouteController::class, 'salesReport'])->name('reports');
Route::get('reports/employees', [RouteController::class, 'employeesReport'])->name('reports');
Route::get('reports/operators', [RouteController::class, 'operatorsReport'])->name('reports');
Route::get('reports/users', [RouteController::class, 'usersReport'])->name('reports');
Route::get('reports/warehouse', [RouteController::class, 'warehouseReport'])->name('reports');
Route::get('payment', [RouteController::class, 'payment'])->name('payment');
Route::get('settings', [RouteController::class, 'settings'])->name('settings');
Route::get('dispatch_returns', [RouteController::class, 'dispatchReturns']);


Route::post('import_cylinder', [CylinderController::class, 'import']);
Route::post('import_customer', [CustomerController::class, 'import']);

//OTP protected routes
Route::middleware(["checkStatus"])->group(function () {
});
Route::get('reports', [RouteController::class, 'reports'])->name('reports');

Route::post('import', [CylinderController::class, 'import'])->name('payment');


Route::get('/barcode', function () {

    return view("barcode");
});

// OTP ROUTES
Route::post('/otp/resend', [OTPController::class, 'resendOTP']);
Route::post("/otp/verify", [OTPController::class, "verifyOTP"]);
Route::get("/confirm_otp", function () {
    return view("modules.otp.index");
})->name("confirm_otp");

Route::get('/foo', function () {
    Artisan::call('storage:link');
});

Route::get('/logout', function () {
    Auth::logout();
    return redirect("/");
});
require __DIR__ . '/auth.php';
