<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        $payment = DB::table("tblpayment")->select(
            "tblpayment.*",
            "tblcustomer.fname",
            "tblcustomer.mname",
            "tblcustomer.lname",
        )
        ->join("tblcustomer","tblcustomer.custno","tblpayment.custno")
        ->where("tblcustomer.deleted","0")
        ->get();

        return response()->json([
            "data" => PaymentResource::collection($payment)
        ]);
    }

    public function reports($customer, $cylinder, $dateFrom, $dateTo)
    {
        $payment = DB::table("tblpayment")->select(
            "tblpayment.*",
            "tblcustomer.fname",
            "tblcustomer.mname",
            "tblcustomer.lname",
        )
        ->join("tblcustomer","tblcustomer.custno","tblpayment.custno")
        ->when($customer !== 'all', function ($q)  use ($customer) {
            return $q->where('tblcustomer.custno', $customer);
        })
        ->when($cylinder !== 'all', function ($q)  use ($cylinder) {
            return $q->where('tblpayment.cylcode', $cylinder);
        })
        ->where("tblcustomer.deleted","0")
        ->whereBetween('tblpayment.createdate', [$dateFrom, $dateTo])
        ->get();

        return response()->json([
            "data" => PaymentResource::collection($payment)
        ]);
    }
}
