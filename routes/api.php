<?php

use App\Http\Controllers\AdminUsersController;
use App\Http\Controllers\CustomerRegistrationController;
use App\Http\Controllers\Mobile\Auth\LoginController;
use App\Http\Controllers\api\v1\AuthenticationController;
use App\Http\Controllers\api\v1\ImageController;
use App\Http\Controllers\api\v1\CustomerController as MobileCustomerController;
use App\Http\Controllers\api\v1\CylinderController as MobileCylinderController;
use App\Http\Controllers\api\v1\VendorController as MobileVendorController;
use App\Http\Controllers\api\v1\DashboardController as MobileDashboardController;
use App\Http\Controllers\api\v1\EmployeeController as MobileEmployeeController;
use App\Http\Controllers\api\v1\WarehouseController as MobileWarehouseController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CylinderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Http\Request;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix("v1")->group(function () {
    // Login and registraion routes do not require authentication
    Route::post("login", [AuthenticationController::class, "login"]);
    Route::post("signup", [AuthenticationController::class, "signUp"]);
    Route::post('verify-otp', [AuthenticationController::class, 'verifyOtp']);
    Route::post('resend-otp', [AuthenticationController::class, 'resendOtp']);

    // Upload a user's profile picture
    Route::post("upload_picture", [ImageController::class, "uploadProfilePicture"]);
    Route::post("upload_id", [ImageController::class, "uploadID"]);
    Route::post("upload_cylinder", [ImageController::class, "uploadCylinder"]);

    // Every other route requires authentication
    Route::group(["middleware" => "auth:sanctum"], function () {
        Route::post("/logout", [AuthenticationController::class, "logout"]);
    });

    // Clients of this API must log out via this route so we can invalidate their access tokens
    Route::post("password_reset", [AuthenticationController::class, "passwordReset"]);
    Route::post("logs", [AuthenticationController::class, "logs"]);
    Route::post("logs/{userid}/{dateFrom}/{dateTo}", [AuthenticationController::class, "logReport"]);

    // Dashboard 
    Route::prefix("dashboard")->group(function () {
    });
    Route::resource("dashboard", MobileDashboardController::class);

    // Customers 
    Route::prefix("customers")->group(function () {
        Route::post("update", [MobileCustomerController::class, "update"]);
        Route::get("trash", [MobileCustomerController::class, "trash"]);
        Route::post("restore", [MobileCustomerController::class, "restore"]);
        Route::post("delete", [MobileCustomerController::class, "delete"]);
    });
    Route::resource("customers", MobileCustomerController::class);

    //Warehouse
    Route::prefix("warehouse")->group(function () {
        Route::post("update", [MobileWarehouseController::class, "update"]);
        Route::post("delete", [MobileWarehouseController::class, "delete"]);
        Route::post("warehouse_dispatch", [MobileWarehouseController::class, "warehouseDispatch"]);
        Route::get("warehouse_dispatches/{fromWarehouse}/{toWarehouse}/{dateFrom}/{dateTo}", [MobileWarehouseController::class, "fetchWarehouseDispatches"]);
        Route::get("fetch_dispatch", [MobileWarehouseController::class, "fetchDispatch"]);
        Route::get("fetch_dispatch/{vendor}/{dateFrom}/{dateTo}", [MobileWarehouseController::class, "fetchDispatchReport"]);
        Route::get("fetch_return", [MobileWarehouseController::class, "fetchReturnCylinder"]);
        Route::get("fetch_return/{vendor}/{dateFrom}/{dateTo}", [MobileWarehouseController::class, "fetchReturnCylinderReport"]);
        Route::get("fetch_exchange", [MobileWarehouseController::class, "fetchExchange"]);
        Route::get("fetch_exchange/{customer}/{cylinder}", [MobileWarehouseController::class, "fetchExchangeReport"]);
        Route::get("fetch_production", [MobileWarehouseController::class, "fetchProduction"]);
        Route::get("fetch_production/{cylinder}/{dateFrom}/{dateTo}", [MobileWarehouseController::class, "fetchProductionReport"]);
    });
    Route::resource("warehouse", MobileWarehouseController::class);

    //settings
    Route::prefix("settings")->group(function () {
        //CRUD ROUTES FOR LOCATION
        Route::get("/location", [SettingsController::class, "fetchLocation"]);
        Route::post("/add_location", [SettingsController::class, "addLocation"]);
        Route::post("/update_location", [SettingsController::class, "updateLocation"]);
        Route::post("/delete_location", [SettingsController::class, "deleteLocation"]);

        //CRUD ROUTES FOR CYLINDER SIZE
        Route::get("/cylinder_size", [SettingsController::class, "fetchCylinderSize"]);
        Route::post("/add_cylinder_size", [SettingsController::class, "addCylinderSize"]);
        Route::post("/update_cylinder_size", [SettingsController::class, "updateCylinderSize"]);
        Route::post("/delete_cylinder_size", [SettingsController::class, "deleteCylinderSize"]);
    });

    // Employees 
    Route::prefix("employees")->group(function () {
        Route::post("update", [MobileEmployeeController::class, "update"]);
        Route::get("trash", [MobileEmployeeController::class, "trash"]);
        Route::post("delete", [MobileEmployeeController::class, "delete"]);
    });
    Route::resource("employees", MobileEmployeeController::class);

    Route::prefix("cylinders")->group(function () {
        Route::get("weight", [MobileCylinderController::class, "fetchCylinderWeight"]);
        Route::get("conditions", [MobileCylinderController::class, "cylinderConditions"]);
        Route::get("owners", [MobileCylinderController::class, "cylinderOwner"]);
        Route::get("payment_modes", [MobileCylinderController::class, "paymentMode"]);
        Route::get("{seachType}/{keyword}", [MobileCylinderController::class, "search"]);
        Route::post("exchange", [MobileCylinderController::class, "exchange"]);
        Route::post("assign_condition", [MobileCylinderController::class, "assignCondition"]);
        Route::post("assign_cylinder", [MobileCylinderController::class, "assignCylinder"]);
        Route::get("capacity", [MobileCylinderController::class, "cylinderCapacity"]);
        Route::get("dropdowns", [MobileCylinderController::class, "dropdowns"]);
    });
    Route::resource("cylinders", MobileCylinderController::class);

    Route::prefix("vendors")->group(function () {
        Route::get("{vendor_no}", [MobileVendorController::class, "vendor"]);
        Route::get("{vendor_no}/outstanding_cylinders", [MobileVendorController::class, "outstandingCylinders"]);
        Route::post("assign", [MobileVendorController::class, "assign"]);
        Route::post("update_dispatch", [MobileVendorController::class, "updateDispatch"]);
        Route::post("return_cylinder", [MobileVendorController::class, "returnCylinder"]);
        Route::get("cylinder/status/{code}", [MobileVendorController::class, "status"]);
    });
    Route::resource("vendors", MobileVendorController::class);
    // });
});

//
Route::get('import_cylinder', [CylinderController::class, 'import']);

//Search and print daily dispatch
Route::post("/search", [SearchController::class, "search"]);
Route::post("/print_dispatch", [SearchController::class, "printDispatch"]);
Route::post("/print_return", [SearchController::class, "printReturn"]);

//Search Customer cylinder
Route::get("/search_customer/{type}/{keyword}", [SearchController::class, "searchCustomer"]);

//Reset password
Route::post("reset_password", [DashboardController::class, "changePassword"]);

// Employees 
Route::prefix("employees")->group(function () {
    Route::post("update", [EmployeeController::class, "update"]);
    Route::get("trash", [EmployeeController::class, "trash"]);
    Route::post("restore", [EmployeeController::class, "restore"]);
    Route::post("delete", [EmployeeController::class, "delete"]);
});
Route::resource("employees", EmployeeController::class);

//Cylinders
Route::prefix("cylinder")->group(function () {
    Route::get("customer", [CylinderController::class, "cylinderCustomer"]);
    Route::get("customer/{customer}/{cylinderFrom}/{cylinderTo}", [CylinderController::class, "customerCylinderReport"]);
    Route::get("{cylinderFrom}/{cylinderTo}", [CylinderController::class, "cylinderReport"]);
    Route::get("{cylinder}/{cylinderFrom}/{cylinderTo}", [CylinderController::class, "cylinderCustomerReport"]);
    Route::post("assign", [CylinderController::class, "assignCylinder"]);
    Route::post("update_assign", [CylinderController::class, "updateAssignCylinder"]);
    Route::get("trash", [CylinderController::class, "trash"]);
    Route::post("update", [CylinderController::class, "update"]);
    Route::post("delete", [CylinderController::class, "delete"]);
    Route::post("delete_assign", [CylinderController::class, "delete_assign"]);
});
Route::resource("cylinder", CylinderController::class);

//logs
Route::prefix("logs")->group(function () {
    Route::get("{userid}/{dateFrom}/{dateTo}", [LogsController::class, "reports"]);
});
Route::resource("logs", LogsController::class);

//Admin users
Route::prefix("users")->group(function () {
    Route::get("fetch_privileges/{userid}", [AdminUsersController::class, "fetchPrivilege"]);
    Route::post("update_priv", [AdminUsersController::class, "updatePriv"]);
    Route::post("update", [AdminUsersController::class, "update"]);
    Route::post("delete", [AdminUsersController::class, "destroy"]);
});
Route::resource("users", AdminUsersController::class);

// Customers 
Route::prefix("customer")->group(function () {
    Route::post("update", [CustomerController::class, "update"]);
    Route::get("trash", [CustomerController::class, "trash"]);
    Route::get("{dateFrom}/{dateTo}", [CustomerController::class, "report"]);
    Route::post("restore", [CustomerController::class, "restore"]);
    Route::post("delete", [CustomerController::class, "delete"]);
});
Route::resource("customer", CustomerController::class);

// Vendors 
Route::prefix("vendor")->group(function () {
    Route::post("update", [VendorController::class, "update"]);
    Route::get("trash", [VendorController::class, "trash"]);
    Route::post("restore", [VendorController::class, "restore"]);
    Route::post("delete", [VendorController::class, "delete"]);
});
Route::resource("vendor", VendorController::class);

//Warehouse
Route::prefix("warehouse")->group(function () {
    Route::post("update", [WarehouseController::class, "update"]);
    Route::post("delete", [WarehouseController::class, "delete"]);
    Route::post("delete_dispatch", [WarehouseController::class, "deleteDispatch"]);
    Route::get("fetch_dispatch", [WarehouseController::class, "fetchDispatch"]);
    Route::get("fetch_dispatch/{vendor}/{dateFrom}/{dateTo}", [WarehouseController::class, "fetchDispatchReport"]);
    Route::post("add_dispatch", [WarehouseController::class, "addDispatch"]);
    Route::post("return_cylinder", [WarehouseController::class, "returnCylinder"]);
    Route::get("fetch_outstanding_cylinders", [WarehouseController::class, "outstandingCylinder"]);
    Route::get("fetch_return", [WarehouseController::class, "fetchReturnCylinder"]);
    Route::get("fetch_return/{vendor}/{dateFrom}/{dateTo}", [WarehouseController::class, "fetchReturnCylinderReport"]);
    Route::get("fetch_exchange", [WarehouseController::class, "fetchExchange"]);
    Route::get("fetch_exchange/{customer}/{cylinder}", [WarehouseController::class, "fetchExchangeReport"]);
    Route::get("fetch_production", [WarehouseController::class, "fetchProduction"]);
    Route::get("fetch_production/{cylinder}/{dateFrom}/{dateTo}", [WarehouseController::class, "fetchProductionReport"]);
    Route::get("fetch_warehouse_dispatch", [WarehouseController::class, "fetchWarehouseDispatch"]);
    Route::post("warehouse_dispatch_delete/{transid}", [WarehouseController::class, "deleteWarehouseDispatch"]);
});
Route::resource("warehouse", WarehouseController::class);

//fetch payments
Route::prefix("payment")->group(function () {
    Route::get("reports/{customer}/{cylinder}/{dateFrom}/{dateTo}", [PaymentController::class, "reports"]);
});
Route::resource("payment", PaymentController::class);

//settings
Route::prefix("settings")->group(function () {
    //CRUD ROUTES FOR LOCATION
    Route::get("/location", [SettingsController::class, "fetchLocation"]);
    Route::post("/add_location", [SettingsController::class, "addLocation"]);
    Route::post("/update_location", [SettingsController::class, "updateLocation"]);
    Route::post("/delete_location", [SettingsController::class, "deleteLocation"]);

    //CRUD ROUTES FOR CYLINDER SIZE
    Route::get("/cylinder_size", [SettingsController::class, "fetchCylinderSize"]);
    Route::post("/add_cylinder_size", [SettingsController::class, "addCylinderSize"]);
    Route::post("/update_cylinder_size", [SettingsController::class, "updateCylinderSize"]);
    Route::post("/delete_cylinder_size", [SettingsController::class, "deleteCylinderSize"]);
});

Route::post('/check', [CylinderController::class, 'check']);
Route::post('/away', [CylinderController::class, 'away']);
