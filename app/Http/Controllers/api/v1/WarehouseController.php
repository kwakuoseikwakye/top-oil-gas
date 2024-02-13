<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DispatchResource;
use App\Http\Resources\ExchangeResource;
use App\Http\Resources\ProductionResource;
use App\Http\Resources\ReturnResource;
use App\Http\Resources\WarehouseResource;
use App\Models\Cylinder;
use App\Models\Log as ModelsLog;
use App\Models\Warehouse;
use App\Models\WarehouseDispatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Location\Facades\Location;

class WarehouseController extends Controller
{
    public function fetchProduction()
    {
        $ex = DB::table("tblproduction")->select(
            "tblstaff.fname",
            "tblstaff.mname",
            "tblstaff.lname",
            "tblproduction.*",
        )
        ->leftJoin("tblstaff","tblstaff.staffid","tblproduction.filled_by")
        ->where("tblproduction.deleted","0")
        ->get();

        return response()->json([
            "data" =>ProductionResource::collection($ex)
        ]);
    }

    public function fetchProductionReport($cylinder, $dateFrom, $dateTo)
    {
        $ex = DB::table("tblproduction")->select(
            "tblstaff.fname",
            "tblstaff.mname",
            "tblstaff.lname",
            "tblproduction.*",
        )
        ->leftJoin("tblstaff","tblstaff.staffid","tblproduction.filled_by")
        ->when($cylinder !== 'all', function ($q)  use ($cylinder) {
            return $q->where('tblproduction.vendor_no', $cylinder);
        })
        ->whereBetween("tblproduction.createdate", [$dateFrom, $dateTo])
        ->where("tblproduction.deleted","0")
        ->get();

        return response()->json([
            "data" =>ProductionResource::collection($ex)
        ]);
    }

    public function fetchExchange()
    {
        $ex = DB::table("tblexchange")->select(
            "tblcustomer.fname",
            "tblcustomer.mname",
            "tblcustomer.lname",
            "tblvendor.fname as vfname",
            "tblvendor.mname as vmname",
            "tblvendor.lname as vlname",
            "tblexchange.*",
        )
        ->join("tblvendor","tblvendor.vendor_no","tblexchange.vendor_no")
        ->join("tblcustomer","tblcustomer.custno","tblexchange.custno")
        ->where("tblexchange.deleted","0")
        ->get();

        return response()->json([
            "data" =>ExchangeResource::collection($ex)
        ]);
    }

    public function fetchExchangeReport($customer, $cylinder)
    {
        $ex = DB::table("tblexchange")->select(
            "tblcustomer.fname",
            "tblcustomer.mname",
            "tblcustomer.lname",
            "tblvendor.fname as vfname",
            "tblvendor.mname as vmname",
            "tblvendor.lname as vlname",
            "tblexchange.*",
        )
        ->join("tblvendor","tblvendor.vendor_no","tblexchange.vendor_no")
        ->join("tblcustomer","tblcustomer.custno","tblexchange.custno")
        ->when($customer !== 'all', function ($q)  use ($customer) {
            return $q->where('tblexchange.custno', $customer);
        })
        ->when($cylinder !== 'all', function ($q)  use ($cylinder) {
            return $q->where('tblexchange.cylcode_old', $cylinder)->orWhere('tblexchange.cylcode_new', $cylinder);
        })
        ->where("tblexchange.deleted","0")
        ->get();

        return response()->json([
            "data" =>ExchangeResource::collection($ex)
        ]);
    }

    public function fetchDispatch()
    {
        $data = DB::table("tbldispatch")->select(
            "tblvendor.fname",
            "tblvendor.mname",
            "tblvendor.lname",
            "tblvendor.phone",
            "tbldispatch.*",
        )
            ->join("tblvendor", "tblvendor.vendor_no", "tbldispatch.vendor_no")
            // ->whereDate("tbldispatch.createdate", "=", date("Y-m-d"))
            ->where("tbldispatch.deleted","0")
            ->orderByDesc("tbldispatch.createdate")
            ->get();

        return response()->json([
            "data" => DispatchResource::collection($data)
        ]);
    }

    public function fetchDispatchReport($vendor, $dateFrom, $dateTo)
    {
        $data = DB::table("tbldispatch")->select(
            "tblvendor.fname",
            "tblvendor.mname",
            "tblvendor.lname",
            "tblvendor.phone",
            "tbldispatch.*",
        )
            ->join("tblvendor", "tblvendor.vendor_no", "tbldispatch.vendor_no")
            ->when($vendor !== 'all', function ($q)  use ($vendor) {
                return $q->where('tbldispatch.vendor_no', $vendor);
            })
            ->whereBetween("tbldispatch.createdate", [$dateFrom, $dateTo])
            ->where("tbldispatch.deleted","0")
            ->orderByDesc("tbldispatch.createdate")
            ->get();

        return response()->json([
            "data" => DispatchResource::collection($data)
        ]);
    }

    public function fetchReturnCylinder()
    {
        $data = DB::table("tblreturn")->select(
            "tblvendor.fname",
            "tblvendor.mname",
            "tblvendor.lname",
            "tblvendor.vendor_no",
            "tblwarehouse.wname",
            "tblvendor.phone",
            "tblreturn.*",
        )
            ->join("tblvendor", "tblvendor.vendor_no", "tblreturn.vendor_no")
            ->join("tblcylinder", "tblcylinder.cylcode", "tblreturn.cylcode")
            ->join("tblwarehouse", "tblwarehouse.wcode", "tblreturn.return_to")
            // ->whereDate("tblreturn.createdate", "=", date("Y-m-d"))
            ->orderByDesc("tblreturn.createdate")
            ->get();

        return response()->json([
            "data" => ReturnResource::collection($data)
        ]);
    }

    public function fetchReturnCylinderReport($vendor, $dateFrom, $dateTo)
    {
        $data = DB::table("tblreturn")->select(
            "tblvendor.fname",
            "tblvendor.mname",
            "tblvendor.lname",
            "tblvendor.vendor_no",
            "tblwarehouse.wname",
            "tblvendor.phone",
            "tblreturn.*",
        )
            ->join("tblvendor", "tblvendor.vendor_no", "tblreturn.vendor_no")
            ->join("tblcylinder", "tblcylinder.cylcode", "tblreturn.cylcode")
            ->join("tblwarehouse", "tblwarehouse.wcode", "tblreturn.return_to")
            ->when($vendor !== 'all', function ($q)  use ($vendor) {
                return $q->where('tblreturn.vendor_no', $vendor);
            })
            ->whereBetween("tblreturn.createdate", [$dateFrom, $dateTo])
            ->orderByDesc("tblreturn.createdate")
            ->get();

        return response()->json([
            "data" => ReturnResource::collection($data)
        ]);
    }

    public function fetchWarehouseDispatches($fromWarehouse, $toWarehouse, $dateFrom, $dateTo)
    {
        $data = WarehouseDispatch::when($fromWarehouse !== 'all', function ($q)  use ($fromWarehouse) {
                return $q->where('tblwarehouse_dispatch.from_warehouse', $fromWarehouse);
            })->when($toWarehouse !== 'all', function ($q)  use ($toWarehouse) {
                return $q->where('tblwarehouse_dispatch.to_warehouse', $toWarehouse);
            })->whereBetween("tbldispatch.createdate", [$dateFrom, $dateTo])
            ->where("tbldispatch.deleted",0)
            ->orderByDesc("tbldispatch.createdate")
            ->get();

        return response()->json([
            "data" => $data
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            "data" => Warehouse::where("deleted", 0)
                ->orderByDesc("createdate")->get(),
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

    public function warehouseDispatch(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "vendorNo" => "required",
                "fromWarehouse" => "required",
                "toWarehouse" => "required",
                "cylinderJson" => "required",
                "createuser" => "required",
            ], [

                "vendorNo.required" => "No vendor name supplied",
                "fromWarehouse.required" => "Please select a source warehouse",
                "toWarehouse.required" => "Please select a destination warehouse",
                "cylinderJson.required" => "No cylinder(s) supplied",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Dispatch failed." . join(". ", $validator->errors()->all()),
                ]);
            }

            // decode json
            DB::beginTransaction();
            $cylinders = json_decode($request->cylinderJson);

            foreach ($cylinders as $key => $cylinder) {

                    $cyl = Cylinder::where("cylcode", $cylinder->cylcode)->where("deleted", 0)->first();

                    if (!empty($cyl)) {
                        $transid = strtoupper(bin2hex(random_bytes(4)));
                        WarehouseDispatch::insert([
                            "transid" => $transid,
                            "vendor_no" => $request->vendorNo,
                            "from_warehouse" => $request->fromWarehouse,
                            "to_warehouse" => $request->toWarehouse,
                            "cylcode" => $cylinder->cylcode,
                            "cylinder_size" => $cyl->size,
                            "createdate" =>  date('Y-m-d H:i:s'),
                            "createuser" => $request->createuser,
                            "deleted" => 0,
                        ]);
                    }
            }

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Warehouse",
                "action" => "Warehouse to Warehouse dispatch",
                "activity" => "Dispatched cylinders successfully from {$request->fromWarehouse} to {$request->toWarehouse} from Mobile",
                "ipaddress" => $userIp,
                "createuser" =>  $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" =>  "Cylinder(s) dispatched successfully",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured during dispatch", [
                "errMsg" => $e->getMessage(),
                "trace" => $e->getTrace(),
            ]);

            return response()->json([
                "ok" => false,
                "msg" => "Request failed. An internal error occured",
                "errMsg" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "wname" => "required",
                "region" => "required",
                "town" => "required",
                "phoneNumber" => "required|numeric",
                "createuser" => "required",
            ], [

                "wname.required" => "No warehouse name supplied",
                "region.required" => "No region supplied",
                "town.required" => "No town supplied",

                // Phone error messages
                "phoneNumber.required" => "No phone number supplied",
                "phoneNumber.numeric" => "Phone number supplied [{$request->phone}] must contain only numbers",
                "phoneNumber.unique" => "Phone number already taken",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Registration failed." . join(". ", $validator->errors()->all()),
                ]);
            }

            DB::beginTransaction();
            $transid = strtoupper(bin2hex(random_bytes(4)));
            Warehouse::insert([
                "transid" => $transid,
                "wcode" => 'WH-' . $transid,
                "wname" => $request->wname,
                "region" => $request->region,
                "town" => $request->town,
                "streetname" => $request->streetname,
                "gpsaddress" => $request->gpsaddress,
                "landmark" => $request->landmark,
                "phone" => $request->phoneNumber,
                "email" => empty($request->email) ? '' : $request->email,
                "createdate" =>  date('Y-m-d H:i:s'),
                "createuser" => $request->createuser,
                "deleted" => 0,
            ]);
            
            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Warehouse",
                "action" => "Add",
                "activity" => "Warehouse added from mobile with id WH-{$transid} successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Registration successful",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured during registration", [
                "errMsg" => $e->getMessage(),
                "trace" => $e->getTrace(),
            ]);

            return response()->json([
                "ok" => false,
                "msg" => "Request failed. An internal error occured",
                "errMsg" => $e->getMessage(),
            ]);
        }
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
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "wcode" => "required",
                "wname" => "required",
                "region" => "required",
                "town" => "required",
                "phoneNumber" => "required|numeric",
                "createuser" => "required",
            ], [

                "wname.required" => "No warehouse name supplied",
                "region.required" => "No region supplied",
                "town.required" => "No town supplied",

                // Phone error messages
                "phoneNumber.required" => "No phone number supplied",
                "phoneNumber.numeric" => "Phone number supplied [{$request->phone}] must contain only numbers",
                "phoneNumber.unique" => "Phone number already taken",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Update failed." . join(". ", $validator->errors()->all()),
                ]);
            }

            DB::beginTransaction();
            DB::table('tblwarehouse')->where("wcode", $request->wcode)->update([
                "wname" => $request->wname,
                "region" => $request->region,
                "town" => $request->town,
                "streetname" => $request->streetname,
                "gpsaddress" => $request->region,
                "landmark" => $request->landmark,
                "phone" => $request->phoneNumber,
                "email" => empty($request->email) ? '' : $request->email,
                "modifydate" =>  date('Y-m-d H:i:s'),
                "modifyuser" => $request->createuser,
            ]);

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Warehouse",
                "action" => "Update",
                "activity" => "Warehouse update from mobile with id {$request->wcode} successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Warehouse updated successful",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured during update", [
                "errMsg" => $e->getMessage(),
                "trace" => $e->getTrace(),
            ]);

            return response()->json([
                "ok" => false,
                "msg" => "Request failed. An internal error occured",
                "errMsg" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "transid" => "required",
            "createuser" => "required",
        ], [
            "transid.required" => "No vendor id supplied",

            "createuser.required" => "No userid supplied",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "ok" => false,
                "msg" => "Warehouse removal failed. " . join(". ", $validator->errors()->all()),
            ]);
        }

        try {
            DB::beginTransaction();
            $customer = Warehouse::where("transid", $request->transid)->where("deleted", 0)->first();

            if (empty($customer)) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Unknown code supplied",
                ]);
            }

            $updated = $customer->update([
                "deleted" => 1,
            ]);

            if (!$updated) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Delete failed",
                ]);
            }

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid,
                "username" => $request->createuser,
                "module" => "Warehouse",
                "action" => "Delete Warehouse",
                "activity" => "Deleted Warehouse with id {$customer->wcode}",
                "ipaddress" => $userIp,
                "createuser" => $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);
            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Warehouse Deleted successfully",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured deleting warehouse", [
                "errMsg" => $e->getMessage(),
                "trace" => $e->getTrace(),
            ]);

            return response()->json([
                "ok" => false,
                "msg" => "Request failed. An internal error occured",
                "errMsg" => $e->getMessage(),
            ]);
        }
    }
}
