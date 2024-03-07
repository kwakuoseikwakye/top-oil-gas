<?php

namespace App\Http\Controllers;

use App\Http\Resources\DispatchResource;
use App\Http\Resources\ExchangeResource;
use App\Http\Resources\OutstandingResource;
use App\Http\Resources\ProductionResource;
use App\Http\Resources\ReturnResource;
use App\Http\Resources\WarehouseDispatchResource;
use App\Http\Resources\WarehouseResource;
use App\Models\Cylinder;
use App\Models\Dispatch;
use App\Models\Log as ModelsLog;
use App\Models\Warehouse;
use App\Models\WarehouseDispatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
        ->leftJoin("tblvendor","tblvendor.vendor_no","tblexchange.vendor_no")
        ->leftJoin("tblcustomer","tblcustomer.custno","tblexchange.custno")
        ->where("tblexchange.deleted","0")
        ->orderByDesc("tblexchange.createdate")
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
        $data = Dispatch::select(
            "tblvendor.fname",
            "tblvendor.mname",
            "tblvendor.lname",
            "tblvendor.phone",
            "tbldispatch.*",
            "tblcustomer_location.*",
            "tblpickup.*"
        )
            ->leftJoin("tblvendor", "tblvendor.vendor_no", "tbldispatch.vendor_no")
            ->leftJoin("tblcustomer_location", "tblcustomer_location.id", "tbldispatch.location_id")
            ->leftJoin("tblpickup", "tblpickup.id", "tbldispatch.pickup_location")
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
            ->leftJoin("tblvendor", "tblvendor.vendor_no", "tblreturn.vendor_no")
            // ->leftJoin("tblcylinder", "tblcylinder.cylcode", "tblreturn.cylcode")
            ->leftJoin("tblwarehouse", "tblwarehouse.wcode", "tblreturn.return_to")
            ->where("tblreturn.deleted","0")
            ->where("tblvendor.deleted", "0")
            ->where("tblwarehouse.deleted", "0")
            // ->where("tblcylinder.deleted", "0")
            // ->whereDate("tblreturn.createdate", "=", date("Y-m-d"))
            ->orderByDesc("tblreturn.createdate")
            ->get();

        return response()->json([
            "data" => ReturnResource::collection($data)
        ]);
    }

    public function outstandingCylinder()
    {
        $data = DB::table("tbldispatch")->select(
            "tblvendor.fname",
            "tblvendor.mname",
            "tblvendor.lname",
            "tblvendor.phone",
            "tbldispatch.*",
        )
            ->join("tblvendor", "tblvendor.vendor_no", "tbldispatch.vendor_no")
            ->where("tbldispatch.deleted","0")
            ->where("tbldispatch.dispatch","0")
            ->orderByDesc("tbldispatch.createdate")
            ->get();

        return response()->json([
            "data" => OutstandingResource::collection($data)
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

    public function addDispatch(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "order_id" => "required",
                "vendor" => "required",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Registration failed." . join(". ", $validator->errors()->all()),
                ]);
            }

        
            DB::beginTransaction();
            Dispatch::where('order_id',$request->order_id)->update([
                "vendor_no" => $request->vendor,
                "status" => Dispatch::EN_ROUTE
            ]);

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Warehouse",
                "action" => "Add vendor dispatch",
                "activity" => "A vendor has been dispatched from Back Office with ID {$request->vendor} successfully",
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
            Log::error("An error occured during signup", [
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

    public function returnCylinder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "vendor" => "required",
                "cylinder" => "required",
                "size" => "required",
            ], [

                "vendor.required" => "No vendor name supplied",
                "cylinder.required" => "No cylinder supplied",
                "size.required" => "No size supplied",

            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Registration failed." . join(". ", $validator->errors()->all()),
                ]);
            }

            // $dispatch = DB::table("tblreturn")->where("deleted", 0)
            // ->where("vendor_no", $request->vendor)
            // ->whereDate("createdate","=",date("Y-m-d"))
            // ->first();

            // if (!empty($dispatch)) {
            //     return response()->json([
            //         "ok" => false,
            //         "msg" => "Vendor already returned cylinder for today"
            //     ]);
            // }


            DB::beginTransaction();
            $transid = strtoupper(bin2hex(random_bytes(4)));
            DB::table("tblreturn")->insert([
                "transid" => $transid,
                "cylcode" => $request->cylinder,
                "vendor_no" => $request->vendor,
                "cylinder_size" => $request->size,
                "return_to" => $request->staff,
                "empty_full" => $request->emptyFull,
                "createdate" =>  date('Y-m-d H:i:s'),
                "createuser" => $request->createuser,
                "deleted" => 0,
            ]);

            DB::table("tbldispatch")->where("transid",$request->transid)->update([
                "modifydate" =>  date('Y-m-d H:i:s'),
                "modifyuser" => $request->createuser,
                "dispatch" => 1,
            ]);

            if ($request->emptyFull === "empty") {
                DB::table("tblproduction")->insert([
                    "transid" => $transid,
                    "cylcode_new" => $request->cylinder,
                    "cylcode_old" => $request->cylinder,
                    "createdate" =>  date('Y-m-d H:i:s'),
                    "createuser" => $request->createuser,
                    "deleted" => 0,
                ]);
            }

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Warehouse",
                "action" => "Vendor return Cylinder",
                "activity" => "A vendor has been dispatched from Back Office with ID {$request->vendor} successfully",
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
            Log::error("An error occured during signup", [
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            "data" => WarehouseResource::collection(Warehouse::where("deleted", 0)
                ->orderByDesc("createdate")->get()),
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
        try {
            $validator = Validator::make($request->all(), [
                "wname" => "required",
                "region" => "required",
                "town" => "required",
                "phoneNumber" => "required|numeric"
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

            // if (!empty($request->email)) {
            //     $mods = DB::table("tblmodule")->get();

            //     foreach ($mods as $mod) {
            //         DB::table("tblmodule_priv")->insert([
            //             "userid" => $request->email,
            //             "modRead" => "1",
            //             "modID" => $mod->modID,
            //             "createdate" => date("Y-m-d"),
            //             "createuser" => "admin",
            //         ]);
            //     }
            // }

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Warehouse",
                "action" => "Add",
                "activity" => "Warehouse added from Back Office with id WH-{$transid} successfully",
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
            Log::error("An error occured during signup", [
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
                "phoneNumber" => "required|numeric"
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
            $transid = strtoupper(bin2hex(random_bytes(4)));
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
                "activity" => "Warehouse update from Back Office with id {$request->wcode} successfully",
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

    public function deleteDispatch(Request $request)
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
                "msg" => "Dispatch removal failed. " . join(". ", $validator->errors()->all()),
            ]);
        }

        try {
            DB::beginTransaction();
            $customer = DB::table("tbldispatch")->where("transid", $request->transid)
            ->where("deleted", 0)
            ->where("dispatch", 0 )->update([
                "deleted" => 1,
            ]);

            // if (empty($customer)) {
            //     return response()->json([
            //         "ok" => false,
            //         "msg" => "Unknown code supplied",
            //     ]);
            // }

            // $updated = $customer->update([
            //     "deleted" => 1,
            // ]);

            if (!$customer) {
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
                "activity" => "Deleted Dispatch with id {$request->createuser}",
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

    public function fetchWarehouseDispatch()
    {
        //fetch warehouse dispatch      
        $warehouse = WarehouseDispatch::where("deleted",0)->orderByDesc("createdate")->get();

        return response()->json([
            "data" => WarehouseDispatchResource::collection($warehouse),
        ]);
    }

    public function deleteWarehouseDispatch($id)
    {     
        DB::table("tblwarehouse_dispatch")->where("transid",$id)->update([
            "deleted" => "1"
        ]);

        return response()->json([
            "ok" => true,
        ]);
    }
}
