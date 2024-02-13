<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerCylinder;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function printDispatch(Request $request)
    {
        $data = DB::table("tbldispatch")->select(
            "tblvendor.fname as vfname",
            "tblvendor.mname as vmname",
            "tblvendor.lname as vlname",
            "tblvendor.phone",
            "tbldispatch.*",
        )
            ->join("tblvendor", "tblvendor.vendor_no", "tbldispatch.vendor_no")
            ->where("tbldispatch.vendor_no", $request->vendor)
            ->where("tbldispatch.deleted", "0")
            ->where("tblvendor.deleted", "0")
            ->where("tbldispatch.dispatch", "0")
            ->whereDate("tbldispatch.createdate", "=", date("Y-m-d"))
            // ->whereBetween('tbldispatch.createdate', [$request->fromtime, $request->totime])
            // ->whereTime('tbldispatch.createdate', '<', $request->fromtime)
            // ->whereTime('tbldispatch.createdate', '>', $request->totime)
            ->orderByDesc("tbldispatch.createdate")
            ->get();
        return response()->json([
            'ok' => true,
            'data' => $data,
        ]);
    }

    public function printReturn(Request $request)
    {
        $data = DB::table("tblreturn")->select(
            "tblvendor.fname as vfname",
            "tblvendor.mname as vmname",
            "tblvendor.lname as vlname",
            "tblvendor.phone",
            "tblreturn.*",
        )
            ->join("tblvendor", "tblvendor.vendor_no", "tblreturn.vendor_no")
            ->where("tblreturn.vendor_no", $request->vendor)
            ->where("tblreturn.deleted", "0")
            ->where("tblvendor.deleted", "0")
            // ->where("tblreturn.dispatch","0")
            ->whereDate("tblreturn.createdate", "=", $request->date)
            ->orderByDesc("tblreturn.createdate")
            ->get();
        return response()->json([
            'ok' => true,
            'data' => $data,
        ]);
    }

    public function search(Request $request)
    {
        $data = DB::table("tbldispatch")->select(
            "tblvendor.fname as vfname",
            "tblvendor.mname as vmname",
            "tblvendor.lname as vlname",
            "tblcustomer.fname",
            "tblcustomer.mname",
            "tblcustomer.lname",
            "tblvendor.phone",
            "tblcustomer.phone AS customerPhone",
            "tbldispatch.*",
            "tblpayment.amount_paid",
            // "tblreturn.empty_full",
            // "tblreturn.return_to",
            // "tblreturn.cylcode as returnCylCode",
            // "tblwarehouse.wname as return_to",
        )
            ->join("tblvendor", "tblvendor.vendor_no", "tbldispatch.vendor_no")
            ->join("tblcustomer_cylinder", "tblcustomer_cylinder.cylcode", "tbldispatch.cylcode")
            ->join("tblcustomer", "tblcustomer.custno", "tblcustomer_cylinder.custno")
            ->join("tblpayment", "tblpayment.custno", "tblcustomer.custno")
            ->where("tbldispatch.vendor_no", "=", $request->vendor)
            // ->where("tbldispatch.dispatch", 1)
            ->whereDate("tbldispatch.createdate", "=", $request->date)
            ->whereDate("tblpayment.createdate", "=", $request->date)
            ->orderByDesc("tbldispatch.createdate")
            ->get();

        $return = DB::table("tblreturn")->where("deleted", 0)->where("vendor_no", $request->vendor)
            ->whereDate("createdate", "=", $request->date)->orderByDesc("createdate")->get();
        return response()->json([
            'ok' => true,
            'data' => $data,
            'return' => $return,
        ]);
    }

    public function searchCustomer($seachType, $keyword)
    {
        switch ($seachType) {
            case 0:
                $data = Customer::where("deleted", 0)->where('phone', $keyword)->with('cylinders', 'cylinders.cylinder', 'customer')->orderBy("createdate", "DESC")->get();
                break;

            case 1:
                $data = CustomerCylinder::where("deleted", 0)->where('cylcode', $keyword)->with('cylinder', 'customer')->orderBy("createdate", "DESC")->get();
                break;

            case 2:
                $data = Customer::where("deleted", 0)->where('id_no', $keyword)->with('cylinders', 'cylinders.cylinder', 'customer')->orderBy("createdate", "DESC")->get();
                break;

            case 3:
                $data = Customer::where("deleted", 0)->where('custno', $keyword)->with('cylinders', 'cylinders.cylinder', 'customer')->orderBy("createdate", "DESC")->get();
                break;

            case 4:
                $data = CustomerCylinder::where("deleted", 0)->where('barcode', $keyword)->with('cylinder', 'customer')->orderBy("createdate", "DESC")->get();
                break;

            default:
                $data = Customer::where("deleted", 0)->where('custno', $keyword)->with('cylinders', 'customer')->orderBy("createdate", "DESC")->get();
                break;
        }

        return response()->json([
            'ok' => true,
            'msg' => 'Request successful',
            'data' => $data,
        ]);
    }
}
