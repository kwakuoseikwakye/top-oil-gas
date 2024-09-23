<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Log as ModelsLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Stevebauman\Location\Facades\Location;
use App\Arkesel\Arkesel as Sms;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    const ALLOWED_USER_TYPES = ["vendor", "warehouse", "admin", "customer"];

    public function signUp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "fname" => "required",
                "lname" => "required",
                "phone" => "required|unique:tbluser,phone",
                "password" => "required|min:8",
                "id_type" => "required",
                "id_no" => "required",
            ], [
                // This has our own custom error messages for each validation
                "fname.required" => "No first name supplied",
                "lname.required" => "No last name supplied",
                // Phone error messages
                "phone.required" => "No phone number supplied",
                "phone.numeric" => "Phone number supplied [{$request->phoneNumber}] must contain only numbers",
                "phone.unique" => "Phone number already taken",

                // ID error messages
                "id_type.required" => "ID Type is required",
                "id_no.required" => "ID number is required",
                "id_link.required" => "Upload ID",

                // Password error messages
                "password.required" => "No password supplied",
                "password.min" => "Your password must be a minimum of 8 characters long",
            ]);

            if ($validator->fails()) {
                return apiErrorResponse("Registration failed. " . join(". ", $validator->errors()->all()), 422);
            }

            DB::beginTransaction();
            $transid = strtoupper(bin2hex(random_bytes(4)));
            User::create([
                "transid" => $transid,
                "userid" => 'CUST-' . $transid,
                "fname" => $request->fname,
                "lname" => $request->lname,
                "username" => $request->fname . ' ' . $request->lname,
                "usertype" => "customer",
                "password" =>  Hash::make($request->password),
                "phone" => empty($request->phone) ? '' : $request->phone,
                "email" => $request->email,
                "picture" => $request->picture
            ]);

            $custno = 'CUST-' . $transid;
            Customer::create($validator->validated() + ['custno' => $custno]);

            if (null !== $request->file("id_link")) {
                $filePath = $request->file("id_link")->store("public/customer");
                Customer::where("custno", $custno)->update([
                    "id_link" => env("APP_URL") . "/" . str_replace("public", "storage", $filePath),
                ]);
            }

            DB::commit();

            $otp = rand(100000, 999999);
            Cache::put('otp_' . $request->phone, $otp, 600);

            $msg = <<<MSG
            Your registration OTP code is {$otp}
            MSG;

            $sms = new Sms('TOP-OIL', env('ARKESEL_SMS_API_KEY'));
            $sms->send($request->phone, $msg);

            return apiSuccessResponse();
        } catch (\Throwable $e) {
            DB::rollBack();
            return apiErrorResponse('Internal error occured', 500, $e);
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
        // return $cachedOtp;

        // if ($cachedOtp && $cachedOtp == $request->otp) {
        // OTP is correct, proceed with user activation or any further steps

        User::where('phone', $request->phone)->update(['verified' => 1]);
        // Optionally, remove the OTP from cache after successful verification
        Cache::forget('otp_' . $request->phone);

        return response()->json([
            "status" => true,
            "message" => "OTP verification successful.",
        ], 200);
        // } else {
        //     return response()->json([
        //         "status" => false,
        //         "message" => "Invalid OTP provided.",
        //     ], 401);
        // }
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
            Cache::put('otp_' . $request->phone, $otp, 240);

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
        Cache::put('otp_' . $request->phone, $otp, 600);

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
}
