<?php

namespace App\Http\Controllers\api\v1;

use App\Arkesel\Arkesel as ASms;
use App\Http\Controllers\Controller;
use App\Models\Log as ModelsLog;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    protected function extractToken(Request $request)
    {
        $authorizationHeader = $request->header('Authorization');
        if (Str::startsWith($authorizationHeader, 'Bearer ')) {
            return Str::substr($authorizationHeader, 7);
        }
        return null;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $token = $this->extractToken($request);

        if ($token) {
            $user = User::where('remember_token', $token)->first();

            if ($user) {
                $cust = Customer::where('tblcustomer.custno', $user->userid)->get();
                return response()->json([
                    'status' => true,
                    'message' => 'Request Successful',
                    'data' => $cust
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized - User not found'
                ], 401);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized - Token not provided or invalid'
            ], 401);
        }
    }

    public function trash()
    {
        return response()->json([
            'ok' => true,
            'msg' => 'Request successful',
            'data' => Customer::where("deleted", 1)->with('cylinders', 'user')->orderBy("createdate", "DESC")->get(),
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
                "phoneNumber" => "required|numeric|unique:tblcustomer,phone",
                // "email" => "required|email|unique:tbluser,email",
                // "idType" => "required",
                // "idNumber" => "required",
                // "idFileLink" => "required",
                // "homeAddress" => "required",
                // "region" => "required",
                // "town" => "required",
                // "streetName" => "required",
                // "landmark" => "required",
                // "gpsaddress" => "required",
                // "owner" => "required",
                // "cylcode" => "required",
                // "size" => "required",
            ], [
                // This has our own custom error messages for each validation
                "title.required" => "No title supplied",

                "firstName.required" => "No first name supplied",
                "lastName.required" => "No last name supplied",

                // Phone error messages
                "phoneNumber.required" => "No phone number supplied",
                "phoneNumber.numeric" => "Phone number supplied [{$request->phoneNumber}] must contain only numbers",
                "phoneNumber.unique" => "Phone number already taken",

                // Gender error messages
                // "gender.required" => "Your gender was not specified",
                // "gender.max" => "Invalid value for gender: expected one of ['m','f'] but got [{$request->gender}]",

                // Email error messages
                // "email.email" => "The supplied email [{$request->email}] is not a valid email",
                // "email.required" => "No email supplied",
                // "email.unique" => "Email already taken",

                // ID error messages
                // "idType.required" => "ID Type is required",
                // "idNumber.required" => "ID number is required",
                // "idFileLink.required" => "Upload ID",

                // Address error messages
                // "homeAddress.required" => "Home Address is required",
                // "region.required" => "Region is required",
                // "town.required" => "Town is required",
                // "streetName.required" => "Streetname is required",
                // "landmark.required" => "Landmark is required",
                // "gpsaddress.required" => "GPS Address is required",

                // "owner.required" => "No owner supplied",
                // "cylcode.required" => "No cylinder number supplied",
                // "size.required" => "No cylinder size supplied",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Registration failed. " . join(". ", $validator->errors()->all()),
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
                "mname" => empty($request->middleName) ? '' : strtoupper($request->middleName),
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

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            if (!empty($request->phoneNumber)) {
                $msg = <<<MSG
             Dear Customer, thank you for signing up for Petrocell cylinder services.
            MSG;

                $sms = new ASms('PETROCELL', env('ARKESEL_SMS_API_KEY'));
                $sms->send($request->phoneNumber, $msg);
            }

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Customer",
                "action" => "Add",
                "activity" => "Customer registered from Mobile with id CUS-{$transid} successfully",
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
                "custno" => 'CUS-' . $transid,
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
                "idFileLink" => "required",
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
                "idFileLink.required" => "Upload ID",

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

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->modifyuser,
                "module" => "Customer",
                "action" => "Update",
                "activity" => "Customer profile updated from Mobile with id {$request->custno} successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $request->modifyuser,
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

    public function restore(Request $request)
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
            $user = User::where("transid", $request->transid)->where("deleted", 1)->first();

            if (empty($user)) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Unknown code supplied",
                ]);
            }

            $user->update([
                "deleted" => 0,
            ]);

            $customer = Customer::where("transid", $request->transid)->where("deleted", 1)->first();

            if (empty($customer)) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Unknown code supplied",
                ]);
            }

            $updated = $customer->update([
                "deleted" => 0,
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
                "action" => "Restore Customer",
                "activity" => "Restored Customer with id {$customer->custno}",
                "ipaddress" => $userIp,
                "createuser" => $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);
            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Customer Restored successfully",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured restore customer", [
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
