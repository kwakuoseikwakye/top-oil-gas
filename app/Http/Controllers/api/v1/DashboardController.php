<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Total customers weekly
        $customerWeekly = DB::table("tblcustomer")->where("deleted", 0)
            ->whereDate('createdate', Carbon::now()->subDays(7))->count();

        // Total customers
        $customersTotal = DB::table("tblcustomer")->where("deleted", 0)->count();

        // Total customers
        $customersToday = DB::table("tblcustomer")->where("deleted", 0)->whereDate("createdate", date('Y-m-d'))->count();

        // Vendor total
        $vendorTotal = DB::table("tblvendor")->where("deleted", 0)->count();

        // Total staff
        $staffTotal = DB::table("tblstaff")->where("deleted", 0)->count();

        // Total cylinders
        $totalCylinders = DB::table("tblcylinder")->where("deleted", 0)->count();

        // Total petrocell cylinders
        $petrocellCylinders = DB::table("tblcylinder")->where("deleted", 0)->where("owner", 'LIKE', 'petrocel%')->count();

        // Total customer cylinders
        $customerCylinders = DB::table("tblcylinder")->where("deleted", 0)->where("owner", 'NOT LIKE', 'petrocel%')->count();

        // Total Payment
        $totalPayment = DB::table("tblpayment")->where("deleted", 0)->sum('amount_paid');

        // Total payment today
        $totalPaymentToday = DB::table("tblpayment")->where("deleted", 0)->whereDate("createdate", date('Y-m-d'))->sum('amount_paid');

        return response()->json([
            'ok' => true,
            'msg' => 'Request successful',
            'data' => [
                'total_customers_weekly' => $customerWeekly,
                'total_customers_today' => $customersToday,
                'total_customers' => $customersTotal,
                'total_vendors' => $vendorTotal,
                'total_staff' => $staffTotal,
                'total_cylinders' => $totalCylinders,
                'total_petrocell_cylinders' => $petrocellCylinders,
                'total_customers_cylinders' => $customerCylinders,
                'total_payment' => $totalPayment,
                'total_payment_today' => $totalPaymentToday,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
