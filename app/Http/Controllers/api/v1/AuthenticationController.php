<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Log as ModelsLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Stevebauman\Location\Facades\Location;

class AuthenticationController extends Controller
{
    const ALLOWED_USER_TYPES = ["vendor", "warehouse", "admin", "customer"];

    public function signUp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "fname" => "required",
                "lname" => "required",
                "phone" => "required|numeric|unique:tblcustomer,phone",
                // "email" => "required|email|unique:tbluser,email",
                "password" => "required|min:8",
                // "username" => "unique:tblvendor,username",
                "id_type" => "required",
                "id_no" => "required",
                // "idFileLink" => "required",
                // "region" => "required",
                // "town" => "required",
                // "streetName" => "required",
                // "landmark" => "required",
                "gpsaddress" => "required",
            ], [
                // This has our own custom error messages for each validation
                "fname.required" => "No first name supplied",
                "lname.required" => "No last name supplied",

                // Usertype errors
                // "userType.required" => "No user type specified. We expect camel case 'userType' with a valid value",
                // "userType.in" => "Invalid usertype: expected one of [" . join(",", self::ALLOWED_USER_TYPES) . "] but got [{$request->userType}]",

                // Phone error messages
                "phone.required" => "No phone number supplied",
                "phone.numeric" => "Phone number supplied [{$request->phoneNumber}] must contain only numbers",
                "phone.unique" => "Phone number already taken",

                // Gender error messages
                // "gender.required" => "Your gender was not specified",
                // "gender.max" => "Invalid value for gender: expected one of ['m','f'] but got [{$request->gender}]",

                // Email error messages
                // "email.email" => "The supplied email [{$request->email}] is not a valid email",
                // "email.required" => "No email supplied",
                // "email.unique" => "Email already taken",

                // Username error messages
                //  "username.unique" => "Username already taken",

                // ID error messages
                "id_type.required" => "ID Type is required",
                "id_no.required" => "ID number is required",
                "id_link.required" => "Upload ID",

                // Address error messages
                //  "region.required" => "Region is required",
                //  "town.required" => "Town is required",
                //  "streetname.required" => "Streetname is required",
                //  "landmark.required" => "Landmark is required",
                "gpsaddress.required" => "GPS Address is required",

                // Password error messages
                "password.required" => "No password supplied",
                // "password.confirm" => "Your passwords do not match",
                "password.min" => "Your password must be a minimum of 8 characters long",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Registration failed. " . join(". ", $validator->errors()->all()),
                ], 422);
            }

            DB::beginTransaction();
            $transid = strtoupper(bin2hex(random_bytes(4)));
            DB::table("tbluser")->insert([
                "transid" => $transid,
                "userid" => 'CUST-' . $transid,
                "fname" => $request->fname,
                "lname" => $request->lname,
                "username" => $request->fname . ' ' . $request->lname,
                "usertype" => "customer",
                "password" =>  Hash::make($request->password),
                "phone" => empty($request->phone) ? '' : $request->phone,
                "email" => $request->email,
                "picture" => $request->picture,
                "deleted" =>  0,
                "createdate" =>  date("Y-m-d H:i:s"),
                "createuser" =>  $request->createuser,
            ]);

            // DB::table('tblvendor')->insert([
            //     "transid" => $transid,
            //     "vendor_no" => 'VND-' . $transid,
            //     "fname" => strtoupper($request->firstName),
            //     "lname" => strtoupper($request->lastName),
            //     "mname" => strtoupper($request->middleName),
            //     "phone" => $request->phoneNumber,
            //     "username" => $request->username,
            //     "email" => $request->email,
            //     "region" => $request->region,
            //     "town" => $request->town,
            //     "streetname" => $request->streetName,
            //     "landmark" => $request->landmark,
            //     "gpsaddress" => $request->gpsaddress,
            //     "longitude" => $request->longitude,
            //     "latitude" => $request->latitude,
            //     "gender" => strtoupper($request->gender),
            //     "dob" => $request->dateOfBirth,
            //     "picture" => $request->picture,
            //     "id_type" => $request->idType,
            //     "id_no" => $request->idNumber,
            //     "id_file_link" => $request->idFileLink,
            //     "createdate" =>  date('Y-m-d H:i:s'),
            //     "createuser" => $request->createuser,
            //     "deleted" => 0,
            //     "approved" => 0,
            // ]);

            $custno = 'CUST-' . $transid;
            Customer::create($validator->validated() + ['custno' => $custno]);

            if (null !== $request->file("id_link")) {
                $filePath = $request->file("id_link")->store("public/student");
                Customer::where("custno", $custno)->update([
                    "id_link" => env("APP_URL") . "/" . str_replace("public", "storage", $filePath),
                ]);
            }
            
            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->fname . ' ' . $request->lname,
                "module" => "Customer",
                "action" => "Add",
                "activity" => "Customer registered from Mobile with id CUST-{$transid} successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $request->fname . ' ' . $request->lname,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Registration successful",
            ], 200);
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

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                // "email" => "required|email|exists:tbluser,email",
                "password" => "required",
                "phone" => "required",
            ], [
                // "email.required" => "Email not supplied",
                // "email.email" => "Invalid email supplied: [{$request->email}]",
                // "email.exists" => "Invalid credentials",
                "phone.required" => "Phone number not supplied",
                "password.required" => "Password not supplied",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Login failed. " . join(". ", $validator->errors()->all()),
                ], 422);
            }

            if (!Auth::attempt($request->only(["phone", "password"]))) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Login failed. Invalid credentials",
                ], 418);
            }

            $user = User::where("phone", $request->phone)->first();

            // No admin can login via the mobile
            if (!in_array(strtolower($user->usertype), self::ALLOWED_USER_TYPES)) {
                return response()->json([
                    "ok" => false,
                    "msg" => "You cannot log in using the mobile client",
                ], 418);
            }

            // Make sure account has been approved
            // if ($user->others->approved == 1) {
            //     return response()->json([
            //         "ok" => false,
            //         "msg" => "Your account has not been approved. Kindly contact admin",
            //     ]);
            // }

            $token = $user->createToken('accessToken')->plainTextToken;
            User::where("phone", $request->phone)->update([
                'remember_token' => $token
            ]);

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $user->username,
                "module" => "Vendor",
                "action" => "Login",
                "activity" => "{$user->username} logged in",
                "ipaddress" => $userIp,
                "createuser" =>  $user->username,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            return response()->json([
                "ok" => true,
                "msg" => "Login successful",
                "data" => [
                    "accessToken" => $token,
                    "user" => $user,
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error("Logging in failed", [
                "request" => $request->all(),
                "msg" => $e->getMessage(),
                "trace" => $e->getTrace(),
            ]);
            return response()->json([
                "ok" => false,
                "msg" => "An internal error occured",
                "trace" => $e->getTrace(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                "ok" => true,
                "msg" => "Logout successful.",
            ]);
        }

        // If the user is not authenticated, return an unauthorized error response
        return response()->json([
            "ok" => false,
            "msg" => "No authenticated user found.",
        ], 401);
    }


    /**
     * Allows user to change their password
     */
    public function passwordReset(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "password" => "required|confirmed|min:8",
                "current_password" => "required",
                "email" => "required|email|exists:tbluser,email",
            ],
            [
                "password.required" => "You have to supply your new password",
                "password.confirmed" => "Your passwords do not match",
                "password.min" => "Your new password must be at least 8 characters long",

                "current_password.required" => "You have to supply your current password",
                "email.required" => "No email supplied",
                "email.exists" => "Unknown email supplied",
                "email.email" => "The email you supplied is invalid",
            ]
        );


        if ($validator->fails()) {
            return response()->json([
                "ok" => false,
                "msg" => "Reset failed. " . join(" ", $validator->errors()->all()),
            ]);
        }

        $authenticatedUser = User::where("email", $request->email)
            ->where("deleted", 0)
            ->first();

        // Return if user not found
        if (empty($authenticatedUser)) {
            return response()->json([
                "ok" => false,
                "msg" => "Unknown user",
            ]);
        }

        if ($authenticatedUser->others->approved == 1) {
            return response()->json([
                "ok" => false,
                "msg" => "This account has not been approved",
            ]);
        }

        // Return if current password is invalid
        if (!Hash::check($request->current_password, $authenticatedUser->password)) {
            $payload["msg"] = "Sorry your current password is incorrect";
            return response()->json([
                "ok" => false,
                "msg" => "Incorrect password",
            ]);
        }

        //create new password
        $password = Hash::make($request->password);


        //update new password with the authenticated user
        try {
            $authenticatedUser->update([
                'password' => $password,
                'modifydate' => date("Y-m-d H:i:s"),
            ]);

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $authenticatedUser->username,
                "module" => "Vendor",
                "action" => "Password Reset",
                "activity" => "{$authenticatedUser->username} changed password",
                "ipaddress" => $userIp,
                "createuser" =>  $authenticatedUser->username,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            return response()->json([
                "ok" => true,
                "msg" => "Password successfully changed",
            ]);
        } catch (\Exception $th) {
            Log::error(
                "Error changing password: " . $th->getMessage(),
                [
                    "request" => $request->all(),
                ]
            );
            return response()->json([
                "ok" => false,
                "msg" => "An internal error occured. Reset failed",
            ]);
        }
    }

    public function logs()
    {
        $logs = DB::table("tbllogs")->orderByDesc("createdate")->get();

        return response()->json([
            "data" => $logs
        ]);
    }

    public function logReport($userid, $dateFrom, $dateTo)
    {
        $logs = DB::table("tbllogs")
            ->when($userid !== 'all', function ($q)  use ($userid) {
                return $q->where('userid', $userid);
            })
            ->whereBetween('createdate', [$dateFrom, $dateTo])
            ->orderByDesc("createdate")->get();

        return response()->json([
            "data" => $logs
        ]);
    }
}
