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
use App\Arkesel\Arkesel as Sms;
use Illuminate\Support\Facades\Cache;

class AuthenticationController extends Controller
{
    const ALLOWED_USER_TYPES = ["vendor", "warehouse", "admin", "customer"];

    // The number of seconds to wait for OTP to be verified
    const VERIFICATION_WAITING_TIME = 300;

    // The allowed length of the OTP
    const ALLOWED_OTP_LENGTH = 6;

    public function signUp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "fname" => "required",
                "lname" => "required",
                "phone" => "required|numeric|unique:tblcustomer,phone",
                // "email" => "required|email|unique:tbluser,email",
                "password" => "required|min:8",
                "id_type" => "required",
                "id_no" => "required",
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

                // Password error messages
                "password.required" => "No password supplied",
                // "password.confirm" => "Your passwords do not match",
                "password.min" => "Your password must be a minimum of 8 characters long",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => false,
                    "message" => "Registration failed. " . join(". ", $validator->errors()->all()),
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

            $custno = 'CUST-' . $transid;
            Customer::create($validator->validated() + ['custno' => $custno]);

            if (null !== $request->file("id_link")) {
                $filePath = $request->file("id_link")->store("public/customer");
                Customer::where("custno", $custno)->update([
                    "id_link" => env("APP_URL") . "/" . str_replace("public", "storage", $filePath),
                ]);
            }

            // $user = User::select('tblcustomer.*', 'tbluser.*')
            //     ->join('tblcustomer', 'tblcustomer.custno', 'tbluser.userid')
            //     ->where('tbluser.phone', $request->phone)->first();

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

            $otp = rand(100000, 999999);
            Cache::put('otp_' . $request->phone, $otp, 60);

            $msg = <<<MSG
            Your registration OTP code is {$otp}
            MSG;

            $sms = new Sms('TOP-OIL', env('ARKESEL_SMS_API_KEY'));
            $sms->send($request->phone, $msg);
            return response()->json([
                "status" => true,
                "message" => "Registration successful",
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured during signup", [
                "errMsg" => $e->getMessage(),
                "trace" => $e->getTrace(),
            ]);

            return response()->json([
                "status" => false,
                "message" => "Request failed. An internal error occured",
                "errMsg" => $e->getMessage(),
            ]);
        }
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "phone" => "required|numeric",
            "otp" => "required|numeric",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => "Sending otp failed. " . join(". ", $validator->errors()->all()),
            ], 422);
        }
        // Retrieve OTP from cache or wherever it was stored
        $cachedOtp = Cache::get('otp_' . $request->phone);

        if ($cachedOtp && $cachedOtp == $request->otp) {
            // OTP is correct, proceed with user activation or any further steps

            User::where('phone', $request->phone)->update(['verified' => 1]);
            // Optionally, remove the OTP from cache after successful verification
            Cache::forget('otp_' . $request->phone);

            return response()->json([
                "status" => true,
                "message" => "OTP verification successful.",
            ], 200);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Invalid OTP provided.",
            ], 401);
        }
    }

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "phone" => "required|numeric",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => "Sending otp failed. " . join(". ", $validator->errors()->all()),
            ], 422);
        }

        // Retrieve the existing OTP from cache
        $otp = Cache::get('otp_' . $request->phone);

        if (!$otp) {
            // If there's no OTP, you might want to generate a new one or return an error
            $otp = rand(100000, 999999);
            Cache::put('otp_' . $request->phone, $otp, 60);

            $msg = <<<MSG
            Your registration OTP code is {$otp}
            MSG;

            $sms = new Sms('TOP-OIL', env('ARKESEL_SMS_API_KEY'));
            $sms->send($request->phone, $msg);
        } else {
            Cache::put('otp_' . $request->phone, $otp, 60);

            $msg = <<<MSG
            Your registration OTP code is {$otp}
            MSG;

            $sms = new Sms('TOP-OIL', env('ARKESEL_SMS_API_KEY'));
            $sms->send($request->phone, $msg);
        }

        return response()->json([
            "status" => true,
            "message" => "OTP has been resent successfully.",
        ], 200);
    }


    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "phone" => "required|numeric",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => "Sending otp failed. " . join(". ", $validator->errors()->all()),
            ], 422);
        }

        Cache::forget('otp_' . $request->phone);

        $authenticatedUser = User::where("phone", $request->phone)
            ->first();

        if (empty($authenticatedUser)) {
            return response()->json([
                "status" => false,
                "message" => "This user does not exist",
            ], 418);
        }

        $otp = rand(100000, 999999);
        Cache::put('otp_' . $request->phone, $otp, 60);

        $msg = <<<MSG
            Your OTP code is {$otp}
            MSG;

        $sms = new Sms('TOP-OIL', env('ARKESEL_SMS_API_KEY'));
        $sms->send($request->phone, $msg);

        return response()->json([
            "status" => true,
            "message" => "OTP sent successfully.",
        ], 200);
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
                    "status" => false,
                    "message" => "Login failed. " . join(". ", $validator->errors()->all()),
                ], 422);
            }

            if (!Auth::attempt($request->only(["phone", "password"]))) {
                return response()->json([
                    "status" => false,
                    "message" => "Login failed. Invalid credentials",
                ], 418);
            }

            $user = User::where("phone", $request->phone)->first();

            // No admin can login via the mobile
            if (!in_array(strtolower($user->usertype), self::ALLOWED_USER_TYPES)) {
                return response()->json([
                    "status" => false,
                    "message" => "You cannot log in using the mobile client",
                ], 418);
            }

            // Make sure account has been approved
            // if ($user->others->approved == 1) {
            //     return response()->json([
            //         "status" => false,
            //         "message" => "Your account has not been approved. Kindly contact admin",
            //     ]);
            // }

            $token = $user->createToken('accessToken')->plainTextToken;
            User::where("phone", $request->phone)->update([
                'remember_token' => $token
            ]);

            $users = User::select('tblcustomer.*', 'tbluser.*')
                ->join('tblcustomer', 'tblcustomer.custno', 'tbluser.userid')
                ->where('tbluser.phone', $request->phone)->first();

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
                "status" => true,
                "message" => "Login successful",
                "data" => [
                    "accessToken" => $token,
                    "user" => $users,
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error("Logging in failed", [
                "request" => $request->all(),
                "message" => $e->getMessage(),
                "trace" => $e->getTrace(),
            ]);
            return response()->json([
                "status" => false,
                "message" => "An internal error occured",
                "trace" => $e->getTrace(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                "status" => true,
                "message" => "Logout successful.",
            ]);
        }

        // If the user is not authenticated, return an unauthorized error response
        return response()->json([
            "status" => false,
            "message" => "No authenticated user found.",
        ], 401);
    }


    /**
     * Allows user to change their password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "password" => "required|min:8",
                "current_password" => "required",
                "phone" => "required|numeric|exists:tbluser,phone",
            ],
            [
                "password.required" => "You have to supply your new password",
                "password.min" => "Your new password must be at least 8 characters long",

                "current_password.required" => "You have to supply your current password",
                "phone.required" => "No phone number supplied",
                "phone.exists" => "Unknown phone number supplied",
                "phone.numeric" => "The phone number you supplied is invalid",
            ]
        );


        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => "Reset failed. " . join(" ", $validator->errors()->all()),
            ], 422);
        }

        $authenticatedUser = User::where("phone", $request->phone)
            ->first();

        // Return if user not found
        if (empty($authenticatedUser)) {
            return response()->json([
                "status" => false,
                "message" => "Unknown user",
            ], 418);
        }

        // Return if current password is invalid
        if (!Hash::check($request->current_password, $authenticatedUser->password)) {
            $payload["msg"] = "Sorry your current password is incorrect";
            return response()->json([
                "status" => false,
                "message" => "Incorrect password",
            ], 418);
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
                "status" => true,
                "message" => "Password successfully changed",
            ]);
        } catch (\Exception $th) {
            Log::error(
                "Error changing password: " . $th->getMessage(),
                [
                    "request" => $request->all(),
                ]
            );
            return response()->json([
                "status" => false,
                "message" => "An internal error occured. Reset failed",
            ], 500);
        }
    }

    public function passwordReset(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "password" => "required|min:8",
                "otp" => "required|min:6",
                "phone" => "required|numeric|exists:tbluser,phone",
            ],
            [
                "password.required" => "You have to supply your new password",
                "password.min" => "Your new password must be at least 8 characters long",
                "otp.min" => "otp must be at least 6 characters long",

                "phone.required" => "No phone number supplied",
                "phone.exists" => "Unknown phone number supplied",
                "phone.numeric" => "The phone number you supplied is invalid",
            ]
        );


        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => "Reset failed. " . join(" ", $validator->errors()->all()),
            ], 422);
        }

        $authenticatedUser = User::where("phone", $request->phone)
            ->first();

        // Return if user not found
        if (empty($authenticatedUser)) {
            return response()->json([
                "status" => false,
                "message" => "Unknown user",
            ], 418);
        }

        $this->verifyOtp($request);
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

            $msg = <<<MSG
            Your password has been reset. If you did not initiate this action, please contact support.
            MSG;

            $sms = new Sms('TOP-OIL', env('ARKESEL_SMS_API_KEY'));
            $sms->send($request->phone, $msg);

            return response()->json([
                "status" => true,
                "message" => "Password reset successfully",
            ]);
        } catch (\Exception $th) {
            Log::error(
                "Error changing password: " . $th->getMessage(),
                [
                    "request" => $request->all(),
                ]
            );
            return response()->json([
                "status" => false,
                "message" => "An internal error occured. Reset failed",
            ], 500);
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
