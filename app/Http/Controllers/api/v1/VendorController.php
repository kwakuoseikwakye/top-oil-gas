<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\OutstandingResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Location\Facades\Location;
use App\Models\Log as ModelsLog;
use App\Models\Vendor;
use App\Models\Cylinder;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'ok' => true,
            'msg' => 'Request successful',
            'data' => Vendor::with('cylinders_not_returned')->where("deleted", 0)->orderBy("createdate", "DESC")->get(),
        ]);
    }

    public function vendor($vendor_no)
    {
        return response()->json([
            'ok' => true,
            'msg' => 'Request successful',
            'data' => Vendor::with('cylinders_not_returned', 'cylinders_not_returned.cylinder')->where("deleted", 0)->where("vendor_no", $vendor_no)->first(),
        ]);
    }

    public function assign(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "vendorNo" => "required",
                "cylinderJson" => "required",
                "createuser" => "required",
            ], [

                "vendorNo.required" => "No vendor name supplied",
                "cylinderJson.required" => "No cylinder(s) supplied",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Registration failed." . join(". ", $validator->errors()->all()),
                ]);
            }

            // decode json
            DB::beginTransaction();
            $cylinders = json_decode($request->cylinderJson);

            foreach ($cylinders as $key => $cylinder) {

                // $dispatch = DB::table("tbldispatch")->where("deleted", 0)
                //     ->where("vendor_no", $request->vendorNo)
                //     ->where("cylcode", $cylinder->cylcode)
                //     ->first();

                // if (empty($dispatch)) {
                $cyl = Cylinder::where("cylcode", $cylinder->cylcode)->where("deleted", 0)->first();

                if (!empty($cyl)) {
                    $transid = strtoupper(bin2hex(random_bytes(4)));
                    DB::table("tbldispatch")->insert([
                        "transid" => $transid,
                        "cylcode" => $cylinder->cylcode,
                        "location" => $request->location,
                        "vendor_no" => $request->vendorNo,
                        "cylinder_size" => $cyl->size,
                        "createdate" =>  date('Y-m-d H:i:s'),
                        "createuser" => $request->createuser,
                        "deleted" => 0,
                        "dispatch" => 0,
                    ]);
                }
                // }
            }

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Warehouse",
                "action" => "Add vendor dispatch",
                "activity" => "Cylinders dispatched from Mobile to user ID {$request->vendorNo} successfully",
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

    public function updateDispatch(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "vendorNo" => "required",
                "cylinderJson" => "required",
                "createuser" => "required",
            ], [
                "vendorNo.required" => "No vendor name supplied",
                "cylinderJson.required" => "No cylinder(s) supplied",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Update failed." . join(". ", $validator->errors()->all()),
                ]);
            }

            // decode json
            DB::beginTransaction();
            $cylinders = json_decode($request->cylinderJson);

            foreach ($cylinders as $key => $cylinder) {

                if ($cylinder->status == 1) {
                    DB::table("tbldispatch")->where("transid", $cylinder->transid)->update([
                        "modifydate" =>  date('Y-m-d H:i:s'),
                        "modifyuser" => $request->createuser,
                        "deleted" => 1,
                    ]);
                }

                if ($cylinder->status == 0) {
                    $dispatch = DB::table("tbldispatch")->where("deleted", 0)
                        ->where("vendor_no", $request->vendorNo)
                        ->where("cylcode", $cylinder->cylcode)
                        ->first();

                    if (empty($dispatch)) {
                        $cyl = Cylinder::where("cylcode", $cylinder->cylcode)->where("deleted", 0)->first();

                        if (!empty($cyl)) {
                            $transid = strtoupper(bin2hex(random_bytes(4)));
                            DB::table("tbldispatch")->insert([
                                "transid" => $transid,
                                "cylcode" => $cylinder->cylcode,
                                "location" => $request->location,
                                "vendor_no" => $request->vendorNo,
                                "cylinder_size" => $cyl->size,
                                "createdate" =>  date('Y-m-d H:i:s'),
                                "createuser" => $request->createuser,
                                "deleted" => 0,
                                "dispatch" => 0,
                            ]);
                        }
                    }
                }
            }

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Warehouse",
                "action" => "Update vendor dispatch",
                "activity" => "Cylinders dispatch updated from Mobile to vendor {$request->vendorNo} successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" =>  "Cylinder(s) dispatch updated successfully",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured during dispatch update", [
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
                "vendorNo" => "required",
                "cylinderJson" => "required",
                "createuser" => "required",
            ], [

                "vendor.required" => "No vendor name supplied",
                "cylinderJson.required" => "No cylinder supplied",
                "size.required" => "No size supplied",

            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Return failed." . join(". ", $validator->errors()->all()),
                ]);
            }

            DB::beginTransaction();
            $cylinders = json_decode($request->cylinderJson);

            foreach ($cylinders as $key => $cylinder) {

                // $return = DB::table("tblreturn")->where("deleted", 0)
                //     ->where("vendor_no", $request->vendorNo)
                //     ->where("cylcode", $cylinder->cylcode)
                //     ->whereDate("createdate", date('Y-m-d'))
                //     ->first();

                // if (empty($return)) {
                if ($cylinder->completed == true) {

                    $cyl = Cylinder::where("cylcode", $cylinder->cylcode)->first();

                    // if (!empty($cyl)) {
                        $transid = strtoupper(bin2hex(random_bytes(4)));
                        DB::table("tblreturn")->insert([
                            "transid" => $transid,
                            "cylcode" => $cylinder->returnCylCode,
                            "vendor_no" => $request->vendorNo,
                            "cylinder_size" => $cylinder->size,
                            "return_to" => $cylinder->wcode,
                            "empty_full" => $cylinder->empty_full,
                            "excess_return" => $cylinder->excessReturn,
                            "completed" => $cylinder->completed,
                            "createdate" =>  date('Y-m-d H:i:s'),
                            "createuser" => $request->createuser,
                            "deleted" => 0,
                        ]);
                    // }

                    DB::table("tbldispatch")->where("transid", $cylinder->transid)->update([
                        "modifydate" =>  date('Y-m-d H:i:s'),
                        "modifyuser" => $request->createuser,
                        "dispatch" => 1,
                    ]);

                    if ($cylinder->empty_full === "empty") {
                        $transid = strtoupper(bin2hex(random_bytes(4)));
                        DB::table("tblproduction")->insert([
                            "transid" => $transid,
                            "cylcode_new" => $cylinder->cylcode,
                            "cylcode_old" => $cylinder->cylcode,
                            "createdate" =>  date('Y-m-d H:i:s'),
                            "createuser" => $request->createuser,
                            "deleted" => 0,
                        ]);
                    }
                }
            }
            // }


            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Warehouse",
                "action" => "Vendor return Cylinder",
                "activity" => "Cylinder(s) has been returned from Mobile with for vendor {$request->vendorNo} successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Cylinder(s) returned successful",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured during return", [
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

    public function status($cylcode)
    {
        $dispatch = DB::table("tbldispatch")->where("deleted", 0)
            ->where("cylcode", $cylcode)
            ->where("dispatch", 0)
            ->first();

        if (empty($dispatch)) {
            $exchange = DB::table("tblexchange")->where("deleted", 0)
                ->where("cylcode_old", $cylcode)
                ->first();

            if (empty($exchange)) {
                return response()->json([
                    'ok' => false,
                    'msg' => 'No record found in dispatch search',
                ]);
            }
            $dispatch = DB::table("tbldispatch")->where("deleted", 0)
                ->where("cylcode", $exchange->cylcode_new)
                ->where("dispatch", 0)
                ->first();
        }

        $cylinder = Cylinder::where("cylcode", $cylcode)->where("deleted", 0)->first();

        if (empty($cylinder)) {
            return response()->json([
                'ok' => false,
                'msg' => 'No record found in cylinder search',
            ]);
        }

        return response()->json([
            'ok' => true,
            'msg' => 'Record found',
            'data' => $dispatch,
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

    public function outstandingCylinders($vendorNo)
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
            ->where("tbldispatch.vendor_no",$vendorNo)
            ->orderByDesc("tbldispatch.createdate")
            ->get();

        return response()->json([
            "data" => OutstandingResource::collection($data)
        ]);
    }
}
