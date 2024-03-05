<?php

namespace App\Http\Controllers;

use App\Models\CustomerCylinder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RouteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        // $this->middleware('checkLogin');
    }
    
    public static function permissions($modURL)
    {
        $permissions = collect(DB::table('tblmodule')->select(
            "p.modID",
            "p.modURL",
            "tblmodule.modName AS 'ParentName'",
            "c.modRead",
        )
            ->leftjoin('tblmodule AS p', 'p.pmodID', 'tblmodule.modID')
            ->leftjoin('tblmodule_priv AS c', 'c.modID', 'p.modID')
            ->where('p.isChild', 1)
            ->where('c.userid', Auth::user()->username)
            ->where('tblmodule.modURL', $modURL)
            ->get())->keyBy('modURL');

        return json_decode(json_encode($permissions), false);
    }

    public function dashboard()
    {
        if(strtolower(Auth::user()->usertype) === "admin"){
            $customerWeekly = DB::table("tblcustomer")->where("deleted",0)
            ->whereDate('createdate', Carbon::now()->subDays(7))->count();
            $customers = DB::table("tblcustomer")->where("deleted",0)->count();
            $vendor = DB::table("tblvendor")->where("deleted",0)->count();
            $staff = DB::table("tblstaff")->where("deleted",0)->count();
            $cylinders = DB::table("tblcylinder")->where("deleted",0)->count();
            $paid = DB::table("tblpayment")->selectRaw("sum(amount_paid) AS total")
            ->where("deleted",0)->first();
            $vendors = DB::table("tblvendor")->where("deleted",0)->get();
            $petrocellCyl = DB::table("tblcylinder")->where("deleted",0)
            ->where("owner","Petrocell")->count();

            $customerCyl = DB::table("tblcylinder")->where("deleted",0)
            ->where("owner","Customer")->count();
            $totalOrders = CustomerCylinder::distinct('order_id')->count();
            // $cylinders 
            return view('main_dashboard',[
                "totalOrders" => $totalOrders,
                "cus" => $customerWeekly,
                "customers" => $customers,
                "paid" => $paid->total,
                "vendors" => $vendor,
                "vends" => $vendors,
                "staff" => $staff,
                "cylinders" => $cylinders,
                "customerCyl" => $customerCyl,
                "petrocellCyl" => $petrocellCyl,
            ]);
        }
        if(strtolower(Auth::user()->usertype) === "vendor"){
            $customerWeekly = DB::table("tblcustomer")->where("deleted",0)
            ->whereDate('createdate', Carbon::now()->subDays(7))->count();
            $customers = DB::table("tblcustomer")->where("deleted",0)->count();
            $vendor = DB::table("tblvendor")->where("deleted",0)->count();
            $staff = DB::table("tblstaff")->where("deleted",0)->count();
            $cylinders = DB::table("tblcylinder")->where("deleted",0)->count();
            // $cylinders 
            return view('vendor_dashboard',[
                "cus" => $customerWeekly,
                "customers" => $customers,
                "vendors" => $vendor,
                "staff" => $staff,
                "cylinders" => $cylinders,
            ]);
        }
        if(strtolower(Auth::user()->usertype) === "warehouse"){
            $customerWeekly = DB::table("tblcustomer")->where("deleted",0)
            ->whereDate('createdate', Carbon::now()->subDays(7))->count();
            $customers = DB::table("tblcustomer")->where("deleted",0)->count();
            $vendor = DB::table("tblvendor")->where("deleted",0)->count();
            $staff = DB::table("tblstaff")->where("deleted",0)->count();
            $cylinders = DB::table("tblcylinder")->where("deleted",0)->count();
            // $cylinders 
            return view('vendor_dashboard',[
                "cus" => $customerWeekly,
                "customers" => $customers,
                "vendors" => $vendor,
                "staff" => $staff,
                "cylinders" => $cylinders,
            ]);
        }
        if(strtolower(Auth::user()->usertype) === "staff"){
            $customerWeekly = DB::table("tblcustomer")->where("deleted",0)
            ->whereDate('createdate', Carbon::now()->subDays(7))->count();
            $customers = DB::table("tblcustomer")->where("deleted",0)->count();
            $vendor = DB::table("tblvendor")->where("deleted",0)->count();
            $staff = DB::table("tblstaff")->where("deleted",0)->count();
            $cylinders = DB::table("tblcylinder")->where("deleted",0)->count();
            // $cylinders 
            return view('vendor_dashboard',[
                "cus" => $customerWeekly,
                "customers" => $customers,
                "vendors" => $vendor,
                "staff" => $staff,
                "cylinders" => $cylinders,
            ]);
        }
       
    }

    public function vendors()
    {
        $vendors = DB::table("tblvendor")->where("deleted",0)->get();
        return view('modules.vendor.index',[
            "vends" => $vendors 
        ]);
    }

    public function operatorsReport()
    {
        return view('modules.reports.vendor.index', [
            "permissions" => $this->permissions('reports'),
        ]);
    }

    public function customers()
    {
        $cylinders = DB::table("tblcylinder")->get();
        $customer = DB::table("tblcustomer")->get();
        $size = DB::table("tblcylinder_size")->get();
        $vendors = DB::table("tblvendor")->get();
        return view('modules.customer.index',[
            "cylinder" => $cylinders,
            "size" => $size,
            "customer" => $customer,
        ]);
    }

    public function customersReport()
    {
        $cylinders = DB::table("tblcylinder")->where("deleted",0)->get();
        $customer = DB::table("tblcustomer")->where("deleted",0)->get();
        $size = DB::table("tblcylinder_size")->where("deleted",0)->get();
        return view('modules.reports.customer.index',[
            "cylinder" => $cylinders,
            "size" => $size,
            "customer" => $customer,
            "permissions" => $this->permissions('reports'),
        ]);
    }

    public function cylinders()
    {
        $cylinders = DB::table("tblcylinder")->where("deleted",0)->get();
        $customer = DB::table("tblcustomer")->where("deleted",0)->get();
        $vendor = DB::table("tblvendor")->where("deleted",0)->get();
        $weights = DB::table("tblcylinder_size")->get();
        return view('modules.cylinder.index',
        [
            "cylinder" => $cylinders,
            "vendor" => $vendor,
            "weights" => $weights,
            "customer" => $customer,]
        );
    }

    public function cylinderReport()
    {
        $cylinders = DB::table("tblcylinder")->where("deleted",0)->get();
        $customer = DB::table("tblcustomer")->where("deleted",0)->get();
        $vendor = DB::table("tblvendor")->where("deleted",0)->get();
        return view('modules.reports.cylinder.index',
        [
            "cylinder" => $cylinders,
            "vendor" => $vendor,
            "customer" => $customer,
            "permissions" => $this->permissions('reports'),
            ]
        );
    }

    public function station()
    {
        return view('modules.station.index');
    }

    public function reports()
    {
        return view('modules.reports.index', [
            "permissions" => $this->permissions('reports'),
        ]);
    }

    public function warehouse()
    {
        $cylinders = DB::table("tblcylinder")->where("deleted",0)->get();
        $vendor = DB::table("tblvendor")->where("deleted",0)->get();
        $staff = DB::table("tblwarehouse")->where("deleted",0)->get();
        $location = DB::table("tblroute")->where("deleted",0)->get();
        return view('modules.warehouse.index',[
            "staff" => $staff,
            "vendor" => $vendor,
            "cylinders" => $cylinders,
            "location" => $location,
        ]);
    }

    public function warehouseReport()
    {
        $cylinders = DB::table("tblcylinder")->where("deleted",0)->get();
        $customers = DB::table("tblcustomer")->where("deleted",0)->get();
        $vendor = DB::table("tblvendor")->where("deleted",0)->get();

        return view('modules.reports.warehouse.index',[
            "customers" => $customers,
            "vendor" => $vendor,
            "cylinders" => $cylinders,
            "permissions" => $this->permissions('reports'),
        ]);
    }

    public function logs()
    {
        return view('modules.logs.index');
    }

    public function employees()
    {
        $role = DB::table("tblrole")->where("deleted",0)->get();
        return view('modules.employees.index',[
            "role" => $role,
        ]);
    }

    public function employeesReport()
    {
         return view('modules.reports.employees.index',[
            "permissions" => $this->permissions('reports'),
        ]);
    }

    public function users()
    {
        return view('modules.users.index');
    }

    public function usersReport()
    {
        $users = User::where("deleted",0)->get();
      
        return view('modules.reports.users.index', [
            "users" => $users,
            "permissions" => $this->permissions('reports'),
        ]);
    }
    
    public function payment()
    {
        return view('modules.payment.index');
    }

    public function salesReport()
    {
        $cylinders = DB::table("tblcylinder")->where("deleted",0)->get();
        $customers = DB::table("tblcustomer")->where("deleted",0)->get();

        return view('modules.reports.payment.index', [
            "cylinders" => $cylinders,
            "customers" => $customers,
            "permissions" => $this->permissions('reports'),
        ]);
    }

    public function settings()
    {
        return view('modules.settings.index');
    }   
    
    public function dispatchReturns()
    {
        return view('modules.dispatch');
    }
}
