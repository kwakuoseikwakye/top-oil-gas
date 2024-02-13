<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Imports\CustomerImport;
use App\Models\Customer;
use App\Models\Log as ModelsLog;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Stevebauman\Location\Facades\Location;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'data' => CustomerResource::collection(Customer::where("deleted", 0)
            ->orderBy("createdate", "DESC")->get()),
        ]);
    }

    public function trash()
    {
        return response()->json([
            'data' => CustomerResource::collection(Customer::where("deleted", 1)
            ->orderBy("createdate", "DESC")->get()),
        ]);
    }

    public function report($dateFrom, $dateTo)
    {
        return response()->json([
            'data' => CustomerResource::collection(Customer::where("deleted", 0)
            ->whereBetween('createdate', [$dateFrom, $dateTo])
            ->orderBy("createdate", "DESC")->get()),
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
                "title" => "required",
                "firstName" => "required",
                "lastName" => "required",
                "phoneNumber" => "required",
                "email" => "required|email|unique:tbluser,email",
                "idType" => "required",
                "idNumber" => "required",
                // "idFileLink" => "required",
                "homeAddress" => "required",
                "region" => "required",
                "town" => "required",
                "streetName" => "required",
                "landmark" => "required",
                "gpsaddress" => "required",
            ], [
                // This has our own custom error messages for each validation
                "title.required" => "No title supplied",

                "firstName.required" => "No first name supplied",
                "lastName.required" => "No last name supplied",

                // Phone error messages
                "phoneNumber.required" => "No phone number supplied",
                // "phoneNumber.numeric" => "Phone number supplied [{$request->phoneNumber}] must contain only numbers",
                // "phoneNumber.unique" => "Phone number already taken",

                // Gender error messages
                "gender.required" => "Your gender was not specified",
                "gender.max" => "Invalid value for gender: expected one of ['m','f'] but got [{$request->gender}]",

                // Email error messages
                "email.email" => "The supplied email [{$request->email}] is not a valid email",
                "email.required" => "No email supplied",
                "email.unique" => "Email already taken",

                // ID error messages
                "idType.required" => "ID Type is required",
                "idNumber.required" => "ID number is required",
                // "idFileLink.required" => "Upload ID",

                // Address error messages
                "homeAddress.required" => "Home Address is required",
                "region.required" => "Region is required",
                "town.required" => "Town is required",
                "streetName.required" => "Streetname is required",
                "landmark.required" => "Landmark is required",
                "gpsaddress.required" => "GPS Address is required",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Registration failed. " . join(". ", $validator->errors()->all()),
                ]);
            }

            $phone = DB::table("tblcustomer")->where("phone",$request->phoneNumber)
            ->where("deleted",0)->first();

            if(empty($phone)){
                return response()->json([
                    "ok" => false,
                    "msg" => "Phone number already taken"
                ]);
            }

            DB::beginTransaction();
            $transid = strtoupper(bin2hex(random_bytes(4)));
            DB::table("tbluser")->insert([
                "transid" => $transid,
                "userid" => 'CUS-' . $transid,
                "fname" => $request->firstName,
                "lname" => $request->lastName,
                "username" => strtolower($transid),
                "usertype" => "customer",
                "password" =>  Hash::make(12345678),
                "phone" => empty($request->phoneNumber) ? '' : $request->phoneNumber,
                "email" => empty($request->email) ? '' : $request->email,
                "picture" => $request->picture,
                "deleted" =>  0,
                "createdate" =>  date("Y-m-d H:i:s"),
                "createuser" =>  $request->createuser,
            ]);

            DB::table('tblcustomer')->insert([
                "transid" => $transid,
                "custno" => 'CUS-' . $transid,
                "title" => $request->title,
                "fname" => strtoupper($request->firstName),
                "lname" => strtoupper($request->lastName),
                "mname" => strtoupper($request->middleName),
                "dob" => $request->dateOfBirth,
                "pob" => $request->placeOfBirth,
                "marital_status" => $request->maritalStatus,
                "occupation" => $request->occupation,
                "home_address" => $request->homeAddress,
                "region" => $request->region,
                "town" => $request->town,
                "streetname" => $request->streetName,
                "landmark" => $request->landmark,
                "gpsaddress" => $request->gpsaddress,
                "phone" => $request->phoneNumber,
                "email" => empty($request->email) ? '' : $request->email,
                "id_type" => $request->idType,
                "id_no" => $request->idNumber,
                "id_link" => $request->idFileLink,
                "gender" => strtoupper($request->gender),
                "picture" => $request->picture,
                "longitude" => $request->longitude,
                "latitude" => $request->latitude,
                "createdate" =>  date('Y-m-d H:i:s'),
                "createuser" => $request->createuser,
                "deleted" => 0,
            ]);

            if (null != $request->hasFile('idimage')) {

                $filePath = $request->file("idimage")->store("public/ids");

                DB::table('tblcustomer')->where("transid", $transid)->update([
                    "id_link" => env("IMAGE_BASE_URL") . "/" . str_replace("public", "storage", $filePath),
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
            
            if (null != $request->hasFile('image')) {

                $filePath = $request->file("image")->store("public/avatars");

                DB::table('tblcustomer')->where("transid", $transid)->update([
                    "picture" => env("IMAGE_BASE_URL") . "/" . str_replace("public", "storage", $filePath),
                ]);
            } 

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Customer",
                "action" => "Add",
                "activity" => "Customer registered from Back Office with id CUS-{$transid} successfully",
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
                "custno" => "required",
                "title" => "required",
                "firstName" => "required",
                "lastName" => "required",
                "phoneNumber" => "required|numeric",
                "email" => "required|email",
                "idType" => "required",
                "idNumber" => "required",
                "homeAddress" => "required",
                "region" => "required",
                "town" => "required",
                "streetName" => "required",
                "landmark" => "required",
                "gpsaddress" => "required",
            ], [
                "custno.required" => "No customer number supplied",

                // This has our own custom error messages for each validation
                "title.required" => "No title supplied",

                "firstName.required" => "No first name supplied",
                "lastName.required" => "No last name supplied",

                // Phone error messages
                "phoneNumber.required" => "No phone number supplied",
                "phoneNumber.numeric" => "Phone number supplied [{$request->phoneNumber}] must contain only numbers",

                // Gender error messages
                "gender.required" => "Your gender was not specified",
                "gender.max" => "Invalid value for gender: expected one of ['m','f'] but got [{$request->gender}]",

                // Email error messages
                "email.email" => "The supplied email [{$request->email}] is not a valid email",
                "email.required" => "No email supplied",

                // ID error messages
                "idType.required" => "ID Type is required",
                "idNumber.required" => "ID number is required",
                
                // Address error messages
                "homeAddress.required" => "Home Address is required",
                "region.required" => "Region is required",
                "town.required" => "Town is required",
                "streetName.required" => "Streetname is required",
                "landmark.required" => "Landmark is required",
                "gpsaddress.required" => "GPS Address is required",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Customer profile update failed. " . join(". ", $validator->errors()->all()),
                ]);
            }

            DB::beginTransaction();
            DB::table("tbluser")->where("userid", $request->custno)->update([
                "fname" => $request->firstName,
                "lname" => $request->lastName,
                "phone" => empty($request->phoneNumber) ? '' : $request->phoneNumber,
                "email" => $request->email,
                "modifydate" =>  date("Y-m-d H:i:s"),
                "modifyuser" =>  $request->modifyuser,
            ]);


            DB::table('tblcustomer')->where("custno", $request->custno)->update([
                "title" => $request->title,
                "fname" => strtoupper($request->firstName),
                "lname" => strtoupper($request->lastName),
                "mname" => strtoupper($request->middleName),
                "dob" => $request->dateOfBirth,
                "pob" => $request->placeOfBirth,
                "marital_status" => $request->maritalStatus,
                "occupation" => $request->occupation,
                "home_address" => $request->homeAddress,
                "region" => $request->region,
                "town" => $request->town,
                "streetname" => $request->streetName,
                "landmark" => $request->landmark,
                "gpsaddress" => $request->gpsaddress,
                "phone" => $request->phoneNumber,
                "email" => $request->email,
                "id_type" => $request->idType,
                "id_no" => $request->idNumber,
                "id_link" => $request->idFileLink,
                "gender" => strtoupper($request->gender),
                "picture" => $request->picture,
                "longitude" => $request->longitude,
                "latitude" => $request->latitude,
                "modifydate" =>  date('Y-m-d H:i:s'),
                "modifyuser" => $request->modifyuser,
            ]);

            if (null != $request->hasFile('idimage')) {

                $filePath = $request->file("idimage")->store("public/ids");

                DB::table('tblcustomer')->where("custno", $request->custno)->update([
                    "id_link" => env("IMAGE_BASE_URL") . "/" . str_replace("public", "storage", $filePath),
                ]);
            } 

            if (null != $request->hasFile('image')) {

                $filePath = $request->file("image")->store("public/avatars");

                DB::table('tblcustomer')->where("custno", $request->custno)->update([
                    "picture" => env("IMAGE_BASE_URL") . "/" . str_replace("public", "storage", $filePath),
                ]);
            } 

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Customer",
                "action" => "Update",
                "activity" => "Customer profile updated from Mobile with id {$request->custno} successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Customer profile updated successfully",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured during customer profile update", [
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
            "transid.required" => "No customer id supplied",

            "createuser.required" => "No userid supplied",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "ok" => false,
                "msg" => "Customer removal failed. " . join(". ", $validator->errors()->all()),
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

            $customer = Customer::where("transid", $request->transid)->where("deleted", 0)->first();

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
                "module" => "Customer",
                "action" => "Delete Customer",
                "activity" => "Deleted Customer with id {$customer->custno}",
                "ipaddress" => $userIp,
                "createuser" => $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);
            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Customer Deleted successfully",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured deleting customer", [
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

    public function import()
    {
        try {
            DB::beginTransaction();
            Excel::import(new CustomerImport, request()->file('file'));

            // $barcodes = Customer::where("deleted",0)->get();

            // foreach ($barcodes as $barcode) {
            //     DB::table("tblcustomer_cylinder")->insert([
            //         'transid' => bin2hex(random_bytes(4)),
            //         'custno' => $barcode->custno,
            //         'barcode' => $barcode->lname,
            //         'cylcode' => $barcode->lname,
            //         'status' => "1",
            //         'createuser' => "admin",
            //         'createdate' => date("Y-m-d H:i:s"),
            //     ]);
            // }

            

            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Upload successful",
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "ok" => false,
                "msg" => "There is an error in your excel file upload",
                "error" => [
                    "msg" => $th->__toString(),
                ]
            ]);
        }
    }
}
