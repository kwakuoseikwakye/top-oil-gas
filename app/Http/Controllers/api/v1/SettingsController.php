<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CylinderSizeResource;
use App\Http\Resources\LocationResource;
use App\Models\CylinderSize;
use App\Models\Location as ModelsLocation;
use App\Models\Log as ModelsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Location\Facades\Location;

class SettingsController extends Controller
{
    /**
     * CRUD FOR LOCATION
     */
    public function fetchLocation()
    {
        $loc = DB::table("tblroute")->where("deleted",0)
        ->orderByDesc("createdate")->get();

        return response()->json([
            "data" =>LocationResource::collection($loc)
        ]);
    }

    public function addLocation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "desc" => "required",
                "createuser" => "required",
            ], [

                "desc.required" => "Location description not supplied",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Adding location failed." . join(". ", $validator->errors()->all()),
                ]);
            }

            DB::beginTransaction();
            $transid = strtoupper(bin2hex(random_bytes(3)));
            DB::table("tblroute")->insert([
                "transid" => $transid,
                "route_code" => 'LCX-' . $transid,
                "route_description" => $request->desc,
                "deleted" =>  0,
                "createdate" =>  date("Y-m-d H:i:s"),
                "createuser" =>  $request->createuser,
            ]);


            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Settings",
                "action" => "Add",
                "activity" => "Location added from Back Office with id {$request->createuser} successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Location added successful",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured during adding location", [
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

    public function updateLocation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "desc" => "required",
                "transid" => "required",
                "createuser" => "required",
            ], [
                "desc.required" => "No location supplied",
                "transid.required" => "No transid supplied",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Location update failed." . join(". ", $validator->errors()->all()),
                ]);
            }

            DB::beginTransaction();
            DB::table("tblroute")->where("route_code", $request->transid)->update([
                "route_description" => $request->desc,
                "modifydate" =>  date("Y-m-d H:i:s"),
                "modifyuser" =>  $request->createuser,
            ]);

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Settings",
                "action" => "Update",
                "activity" => "Location updated from Back Office with id {$request->createuser} successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Update successful",
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

    public function deleteLocation(Request $request)
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
                "msg" => "Location removal failed. " . join(". ", $validator->errors()->all()),
            ]);
        }

        try {
            DB::beginTransaction();
            $route = ModelsLocation::where("transid", $request->transid)->where("deleted", 0)->first();

            if (empty($route)) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Unknown code supplied",
                ]);
            }

           $route->update([
                "deleted" => 1,
            ]);

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid,
                "username" => $request->createuser,
                "module" => "Settings",
                "action" => "Delete location",
                "activity" => "Deleted location with id {$request->createuser}",
                "ipaddress" => $userIp,
                "createuser" => $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);
            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "location Deleted successfully",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured deleting location", [
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
     * CRUD FOR CYLINDER SIZE
     */
    public function fetchCylinderSize()
    {
        $loc = DB::table("tblcylinder_size")->where("deleted",0)
        ->orderByDesc("createdate")->get();

        return response()->json([
            "data" =>CylinderSizeResource::collection($loc)
        ]);
    }

    public function addCylinderSize(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "desc" => "required",
                "createuser" => "required",
            ], [

                "desc.required" => "Cylinder size not supplied",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Adding cylinder size failed." . join(". ", $validator->errors()->all()),
                ]);
            }

            DB::beginTransaction();
            $transid = strtoupper(bin2hex(random_bytes(3)));
            DB::table("tblcylinder_size")->insert([
                "transid" => $transid,
                "description" => $request->desc,
                "deleted" =>  0,
                "createdate" =>  date("Y-m-d H:i:s"),
                "createuser" =>  $request->createuser,
                "modifydate" =>  date("Y-m-d H:i:s"),
                "modifyuser" =>  $request->createuser,
            ]);


            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Settings",
                "action" => "Add",
                "activity" => "Cylinder size added from Back Office with id {$request->createuser} successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Cylinder size added successful",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured during addition", [
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

    public function updateCylinderSize(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "desc" => "required",
                "transid" => "required",
                "createuser" => "required",
            ], [
                "desc.required" => "No size supplied",
                "transid.required" => "No transid supplied",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Cylinder size update failed." . join(". ", $validator->errors()->all()),
                ]);
            }

            DB::beginTransaction();
            DB::table("tblcylinder_size")->where("transid", $request->transid)->update([
                "description" => $request->desc,
                "modifydate" =>  date("Y-m-d H:i:s"),
                "modifyuser" =>  $request->createuser,
            ]);

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Settings",
                "action" => "Update",
                "activity" => "cylinder size updated from Back Office with id {$request->createuser} successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Update successful",
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

    public function deleteCylinderSize(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "transid" => "required",
            "createuser" => "required",
        ], [
            "transid.required" => "No cylinder id supplied",

            "createuser.required" => "No userid supplied",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "ok" => false,
                "msg" => "Vendor removal failed. " . join(". ", $validator->errors()->all()),
            ]);
        }

        try {
            DB::beginTransaction();
            $route = CylinderSize::where("transid", $request->transid)->where("deleted", 0)->first();

            if (empty($route)) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Unknown code supplied",
                ]);
            }

           $route->update([
                "deleted" => 1,
            ]);

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid,
                "username" => $request->createuser,
                "module" => "Settings",
                "action" => "Delete Cylinder size",
                "activity" => "Deleted cylinder size with id {$request->createuser}",
                "ipaddress" => $userIp,
                "createuser" => $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);
            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Cylinder size Deleted successfully",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured deleting cylinder size", [
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
