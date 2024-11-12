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
use App\Arkesel\Arkesel as Sms;
use App\Services\OtpService;
use App\Services\SmsService;

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
            'data' => CustomerResource::collection(Customer::all()),
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
    public function store(Request $request, SmsService $sms)
    {
        try {
            $validator = Validator::make($request->all(), [
                "fname" => "required",
                "lname" => "required",
                "phone" => "required|unique:users,phone",
                "id_type" => "required",
                "id_no" => "required",
            ], [
                "fname.required" => "No first name supplied",
                "lname.required" => "No last name supplied",
                "phone.required" => "No phone number supplied",
                "phone.unique" => "Phone number already taken",
                "id_type.required" => "ID Type is required",
                "id_no.required" => "ID number is required",
            ]);

            if ($validator->fails()) {
                return apiErrorResponse("Registration failed. " . join(". ", $validator->errors()->all()));
            }

            DB::beginTransaction();

            $pass = uniqid();

            $customer = Customer::create([
                "fname" => strtoupper($request->fname),
                "lname" => strtoupper($request->lname),
                "id_type" => $request->id_type,
                "id_no" => $request->id_no,
                "agent_code" => $request->agent_code,
            ]);
            User::create([
                'customer_id' => $customer->id,
                "username" => "{$request->fname} {$request->lname}",
                "usertype" => "customer",
                "password" =>  Hash::make($pass),
                "phone" => empty($request->phone) ? '' : $request->phone,
            ]);

            if (null != $request->hasFile('idimage')) {

                $filePath = $request->file("idimage")->store("public/ids");

                Customer::where("id", $customer->id)->update([
                    "id_link" => env("IMAGE_BASE_URL") . "/" . str_replace("public", "storage", $filePath),
                ]);
            }


            if (null != $request->hasFile('image')) {

                $filePath = $request->file("image")->store("public/avatars");

                Customer::where("id", $customer->id)->update([
                    "picture" => env("IMAGE_BASE_URL") . "/" . str_replace("public", "storage", $filePath),
                ]);
            }

            DB::commit();
            $msg = <<<MSG
            Hi {$request->fname},
            Thanks for registering with TOPOIL.
            Kindly use the following credentials to login
            into our mobile app. Your password is {$pass}
            MSG;

            $sms->sendMessage($request->phone, $msg);

            return apiSuccessResponse('Customer added successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return apiErrorResponse('An error occurred adding customer', 500, $e);
        }
    }

    public function addLocation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "name" => "required",
                "address" => "required",
                "phoneNumber" => "required|numeric",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Registration failed. " . join(". ", $validator->errors()->all()),
                ]);
            }

            DB::beginTransaction();

            DB::table('tblcustomer_location')->insert([
                "custno" => $request->custno,
                "name" => strtoupper($request->name),
                "additional_info" => $request->additional_info,
                "address" => $request->address,
                "phone1" => $request->phoneNumber
            ]);

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Customer",
                "action" => "Add",
                "activity" => "Customer locaiton added from Back Office with id {$request->custno} successfully",
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
                "firstName" => "required",
                "lastName" => "required",
                "phoneNumber" => "required|numeric",
                "idType" => "required",
                "idNumber" => "required",
            ], [
                "custno.required" => "No customer number supplied",
                "firstName.required" => "No first name supplied",
                "lastName.required" => "No last name supplied",
                // Phone error messages
                "phoneNumber.required" => "No phone number supplied",
                "phoneNumber.numeric" => "Phone number supplied [{$request->phoneNumber}] must contain only numbers",

                // ID error messages
                "idType.required" => "ID Type is required",
                "idNumber.required" => "ID number is required",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Customer profile update failed. " . join(". ", $validator->errors()->all()),
                ]);
            }

            $phone = Customer::where('phone', $request->phoneNumber)->first();
            DB::beginTransaction();
            if (!empty($phone)) {
                DB::table("tbluser")->where("userid", $request->custno)->update([
                    "fname" => $request->firstName,
                    "lname" => $request->lastName,
                    // "phone" => empty($request->phoneNumber) ? '' : $request->phoneNumber,
                    "modifydate" =>  date("Y-m-d H:i:s"),
                    "modifyuser" =>  $request->modifyuser,
                ]);


                DB::table('tblcustomer')->where("custno", $request->custno)->update([
                    "fname" => strtoupper($request->firstName),
                    "lname" => strtoupper($request->lastName),
                    // "phone" => $request->phoneNumber,
                    "id_type" => $request->idType,
                    "id_no" => $request->idNumber,
                    "id_link" => $request->idFileLink,
                    "picture" => $request->picture,
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
            } else {
                $validator = Validator::make($request->all(), [
                    "phoneNumber" => "unique|tbluser,phone",
                ], [
                    // Phone error messages
                    "phoneNumber.unique" => "Phone already taken",
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        "ok" => false,
                        "msg" => "Customer profile update failed. " . join(". ", $validator->errors()->all()),
                    ]);
                }

                DB::table("tbluser")->where("userid", $request->custno)->update([
                    "fname" => $request->firstName,
                    "lname" => $request->lastName,
                    "phone" => empty($request->phoneNumber) ? '' : $request->phoneNumber,
                    "modifydate" =>  date("Y-m-d H:i:s"),
                    "modifyuser" =>  $request->modifyuser,
                ]);


                DB::table('tblcustomer')->where("custno", $request->custno)->update([
                    "title" => $request->title,
                    "fname" => strtoupper($request->firstName),
                    "lname" => strtoupper($request->lastName),
                    "phone" => $request->phoneNumber,
                    "id_type" => $request->idType,
                    "id_no" => $request->idNumber,
                    "id_link" => $request->idFileLink,
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
            }
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

            User::where("userid", $request->transid)->delete();
            Customer::where("custno", $request->transid)->delete();

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid,
                "username" => $request->createuser,
                "module" => "Customer",
                "action" => "Delete Customer",
                "activity" => "Deleted Customer with id {$request->transid}",
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
}
