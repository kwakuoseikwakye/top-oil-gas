<?php

namespace App\Http\Controllers;

use App\Http\Resources\VendorResource;
use App\Models\Log as ModelsLog;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Location\Facades\Location;

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
            "data" => VendorResource::collection(Vendor::where("deleted",0)
            ->orderByDesc("createdate")->get()),
        ]);
    }

    public function trash()
    {
        return response()->json([
            "data" => VendorResource::collection(Vendor::where("deleted",1)
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
                "firstName" => "required",
                "lastName" => "required",
                "phoneNumber" => "required|numeric|unique:tblvendor,phone",
                "email" => "required|email|unique:tbluser,email",
            ], [

                "firstName.required" => "No first name supplied",
                "lastName.required" => "No last name supplied",

                // Phone error messages
                "phoneNumber.required" => "No phone number supplied",
                "phoneNumber.numeric" => "Phone number supplied [{$request->phone}] must contain only numbers",
                "phoneNumber.unique" => "Phone number already taken",

                // Gender error messages
                "gender.required" => "Your gender was not specified",
                "gender.max" => "Invalid value for gender: expected one of ['m','f'] but got [{$request->gender}]",

                // Email error messages
                "email.email" => "The supplied email [{$request->email}] is not a valid email",
                "email.required" => "No email supplied",
                "email.unique" => "Email already taken",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Registration failed." . join(". ", $validator->errors()->all()),
                ]);
            }

            
            DB::beginTransaction();
            $transid = strtoupper(bin2hex(random_bytes(4)));
            DB::table("tbluser")->insert([
                "transid" => $transid,
                "userid" => 'VN-' . $transid,
                "fname" => $request->firstName,
                "lname" => $request->lastName,
                "username" => strtolower("VN-". $transid),
                "usertype" => "vendor",
                "password" =>  Hash::make(12345678),
                "phone" => empty($request->phoneNumber) ? '' : $request->phoneNumber,
                "email" => empty($request->email) ? '' : $request->email,
                "picture" => $request->picture,
                "deleted" =>  0,
                "createdate" =>  date("Y-m-d H:i:s"),
                "createuser" =>  $request->createuser,
            ]);

            DB::table('tblvendor')->insert([
                "transid" => $transid,
                "vendor_no" => 'VN-' . $transid,
                "fname" => strtoupper($request->firstName),
                "lname" => strtoupper($request->lastName),
                "mname" => strtoupper($request->middleName),
                "username" => strtolower('VN-' . $transid),
                "region" => $request->region,
                "town" => $request->town,
                "streetname" => $request->streetName,
                "landmark" => $request->landmark,
                "gpsaddress" => $request->gpsaddress,
                "phone" => $request->phoneNumber,
                "email" => empty($request->email) ? '' : $request->email,
                "id_type" => $request->idType,
                "id_no" => $request->idNumber,
                "gender" => strtoupper($request->gender),
                "longitude" => $request->longitude,
                "latitude" => $request->latitude,
                "createdate" =>  date('Y-m-d H:i:s'),
                "createuser" => $request->createuser,
                "deleted" => 0,
                "approved" => 0,
            ]);

            if (null !== $request->file("image")) {
                $path = $request->file("image")->store("public/vendor");

                Vendor::where("transid", $transid)->update([
                    "picture" => env("APP_URL") . "/storage/vendor/" . explode('/', $path)[2],
                ]);

            }

            if (!empty($request->email)) {
                $mods = DB::table("tblmodule")->get();

                foreach ($mods as $mod) {
                    DB::table("tblmodule_priv")->insert([
                        "userid" => $request->email,
                        "modRead" => "1",
                        "modID" => $mod->modID,
                        "createdate" => date("Y-m-d"),
                        "createuser" => "admin",
                    ]);
                }
            }

            if (null !== $request->file("idimage")) {
                $path = $request->file("idimage")->store("public/vendor");

                Vendor::where("transid", $transid)->update([
                    "id_file_link" => env("APP_URL") . "/storage/vendor/" . explode('/', $path)[2],
                ]);
            }

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Vendor",
                "action" => "Add",
                "activity" => "Vendor added from Back Office with id VN-{$transid} successfully",
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
                "vendor" => "required",
                "gender" => "required",
                "firstName" => "required",
                "lastName" => "required",
                "phoneNumber" => "required|numeric",
                // "email" => "required|email|unique:tbluser,email",
            ], [
                "vendor.required" => "No vendor supplied",
                "fname.required" => "No first name supplied",
                "lname.required" => "No last name supplied",

                // Phone error messages
                "phoneNumber.required" => "No phone number supplied",
                "phoneNumber.numeric" => "Phone number supplied [{$request->phone}] must contain only numbers",

                // Gender error messages
                "gender.required" => "Your gender was not specified",
                "gender.max" => "Invalid value for gender: expected one of ['m','f'] but got [{$request->gender}]",

                // Email error messages
                "email.email" => "The supplied email [{$request->email}] is not a valid email",
                "email.required" => "No email supplied",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Vendor update failed." . join(". ", $validator->errors()->all()),
                ]);
            }

            $checkEmailExist = DB::table("tbluser")->where("email",$request->email)->first();

            if (!empty($checkEmailExist)) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Email already taken"
                ]);
            }

            DB::beginTransaction();
            $transid = strtoupper(bin2hex(random_bytes(4)));
            DB::table("tbluser")->where("userid", $request->vendor)->update([
                "fname" => $request->firstName,
                "lname" => $request->lastName,
                "phone" => empty($request->phoneNumber) ? '' : $request->phoneNumber,
                "email" => empty($request->email) ? '' : $request->email,
                "modifydate" =>  date("Y-m-d H:i:s"),
                "modifyuser" =>  $request->createuser,
            ]);

            DB::table('tblvendor')->where("vendor_no", $request->vendor)->update([
                "fname" => strtoupper($request->firstName),
                "lname" => strtoupper($request->lastName),
                "mname" => strtoupper($request->middleName),
                "region" => $request->region,
                "town" => $request->town,
                "streetname" => $request->streetName,
                "landmark" => $request->landmark,
                "gpsaddress" => $request->gpsaddress,
                "phone" => $request->phoneNumber,
                "email" => empty($request->email) ? '' : $request->email,
                "id_type" => $request->idType,
                "id_no" => $request->idNumber,
                "gender" => strtoupper($request->gender),
                "longitude" => $request->longitude,
                "latitude" => $request->latitude,
                "modifydate" =>  date('Y-m-d H:i:s'),
                "modifyuser" => $request->createuser,
            ]);

            if (null !== $request->file("image")) {
                $path = $request->file("image")->store("public/vendor");

                Vendor::where("transid", $transid)->update([
                    "picture" => env("APP_URL") . "/storage/vendor/" . explode('/', $path)[2],
                ]);

            }

            if (null !== $request->file("idimage")) {
                $path = $request->file("idimage")->store("public/vendor");

                Vendor::where("transid", $transid)->update([
                    "id_file_link" => env("APP_URL") . "/storage/vendor/" . explode('/', $path)[2],
                ]);
            }

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Vendor",
                "action" => "Update",
                "activity" => "Vendor updated from Back Office with id VN-{$transid} successfully",
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
                "msg" => "Vendor removal failed. " . join(". ", $validator->errors()->all()),
            ]);
        }

        try {
            DB::beginTransaction();
            $user = User::where("transid", $request->transid)->where("deleted", 0)->first();

            if (empty($user)) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Unknown code supplied",
                ]);
            }

           $user->update([
                "deleted" => 1,
            ]);

            $customer = Vendor::where("transid", $request->transid)->where("deleted", 0)->first();

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
                "module" => "Vendor",
                "action" => "Delete Vendor",
                "activity" => "Deleted vendor with id {$customer->custno}",
                "ipaddress" => $userIp,
                "createuser" => $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);
            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Vendor Deleted successfully",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured deleting vendor", [
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
