<?php

namespace App\Http\Controllers;

use App\Arkesel\Arkesel as ASms;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class OTPController extends Controller
{
    // The number of seconds to wait for OTP to be verified
    const VERIFICATION_WAITING_TIME = 300;

    // The allowed length of the OTP
    const ALLOWED_OTP_LENGTH = 6;

    public static function generateAndStoreOTP(Request $request, $modID)
    {    
        $otp = strtoupper(bin2hex(random_bytes(3)));
        $OTPSessionData = $request->session()->get("OTPSessionData");
        
        if (empty($OTPSessionData)) {            
            $request->session()->put("OTPSessionData", [
                    $modID => ["otp" => Hash::make($otp), "timestamp" => time(), "verified" => false]
                ]
            );

            return $otp;
        }
        
        $OTPSessionData[$modID] = ["otp" => Hash::make($otp), "timestamp" => time(), "verified" => false];
        $request->session()->put("OTPSessionData", $OTPSessionData);
        return $otp;     
    }

    public static function OTPIsVerified(Request $request, $modID){
        $OTPSessionData = $request->session()->get('OTPSessionData');
        if (empty($OTPSessionData)) {
            return false;
        }

        if (empty($OTPSessionData[$modID])) {
            return false;
        }
        
        return $OTPSessionData[$modID]["verified"];              
    }

    public static function sendOTP($otp, $modURL)
    {
        try {
            $user = Auth::user()->fname .  " " . Auth::user()->lname;
            $date = gmdate("jS M Y, h:iA");
            
            $msg = <<<MSG
            Access required to a view

            OTP: {$otp}
            View: {$modURL}
            User: {$user}
            Date: {$date}
            MSG;
            
            $sms = new ASms('PETROCELL.', env('ARKESEL_SMS_API_KEY'));
            $sms->send(env('DIRECTOR_PHONE_NUMBER'), $msg);

        } catch (\Throwable $e) {
            Log::error($e->getMessage(), [
                "trace" => $e->getTrace(),
            ]);
            return response()->json([
                "ok" => false,
                "msg" => "Account creation failed. An internal error occured",
                "error" => [
                    "msg" => $e->getMessage(),
                    "file" => $e->getFile(),
                    "line" => $e->getLine(),
                ]
            ]);
        }
    }


    public function verifyOTP(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "otp" => "required",
                "modID" => "required",
                "modName" => "required",
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                "ok" => false,
                "msg" => "Validation failed. " . join(" ", $validator->errors()->all()),
                "error" => [
                    "msg" => "Request validation failed" . join(" ", $validator->errors()->all()),
                    "fix" => "Please fix all validation errors",
                ]
            ]);
        }

        $modSessionData = $request->session()->get("OTPSessionData")[$request->modID];
        
        if ((time() - (int)$modSessionData["timestamp"]) > self::VERIFICATION_WAITING_TIME) {
            return response()->json([
                "ok" => false,
                "msg" => "Verification failed. OTP code has expired. Request a new one",
            ]);
        }

        if (Hash::check($modSessionData["otp"], $request->otp)) {
            return response()->json([
                "ok" => false,
                "msg" => "Verification failed! Incorrect OTP. Please try again",
            ]);
        }

        $modSessionData["verified"] = true;
        $request->session()->put("OTPSessionData.{$request->modID}", $modSessionData);

        return response()->json([
            "ok" => true,
            "msg" => "Verified",
            "data" => [
                "url" => $request->modName,
            ]
        ]);
    }

    
    public function resendOTP(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    "modName" => "required",
                    "modID" => "required",
                ]
            );
    
            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Could not resend OTP. " . join(" ", $validator->errors()->all()),
                    "error" => [
                        "msg" => "Request validation failed" . join(" ", $validator->errors()->all()),
                        "fix" => "Please fix all validation errors",
                    ]
                ]);
            }

            OTPController::sendOTP(
                OTPController::generateAndStoreOTP($request, $request->modID), 
                $request->modName
            );

            return response()->json([
                "ok" => true,
                "msg" => "OTP resent",
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                "ok" => false,
                "msg" => "Generating new OTP failed. An internal error occured",
                "error" => [
                    "msg" => $e->getMessage(),
                ]
            ]);
        }
    }
    
}
