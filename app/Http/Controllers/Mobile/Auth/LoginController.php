<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                "email" => "required",
                "password" => "required",
            ]
        );

        // Payload to be sent with response
        $payload = [
            "ok" => false,
        ];

        if ($validator->fails()) {
            $payload["msg"] = "Login failed. No email or password";
            $payload["error"] = [
                "msg" => join(" ", $validator->errors()->all()),
                "fix" => "Kindly fix the above errors",
            ];
            return response($payload);
        }

        $authenticatedUser = DB::table("user")->where("gmail", strtolower($request->email))
            ->where("active", "1")
            ->where('password',md5($request->password))
            // ->where("usertype","Attendant")
            ->first();


        // Return if no user is found
        if (empty($authenticatedUser)) {
            return response()->json([
                "ok" => false,
                "msg" => "Login failed. Wrong email or password"
            ]);
        }


        return response()->json([
            "ok" => true,
            "msg" => "Login successful",
            "data" => $authenticatedUser
        ]);
    }
}
