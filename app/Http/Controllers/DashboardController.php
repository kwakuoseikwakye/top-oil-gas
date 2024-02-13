<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function changePassword(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "new_password" => "required",
                "current_password" => "required",
                "userid" => "required",
            ]
        );

        // Payload to be sent with response
        $payload = [
            "ok" => false,
        ];

        if ($validator->fails()) {
            $payload["msg"] = "Sorry, all fields are required";
            $payload["error"] = [
                "msg" => join(" ", $validator->errors()->all()),
                "fix" => "Kindly fix the above errors",
            ];
            return response($payload);
        }

        $authenticatedUser = User::where("transid", $request->userid)
            ->where("deleted", "0")
            ->first();

        // Return if user not found
        if (empty($authenticatedUser)) {
            $payload["msg"] = "Sorry, you cannot change password for this account";
            return response()->json($payload);
        }


        // Return if old password is invalid
        if (!Hash::check($request->current_password, $authenticatedUser->password)) {
            $payload["msg"] = "Sorry, wrong current  password";
            return response()->json($payload);
        }

        //create new password
        $password = password_hash($request->new_password, PASSWORD_DEFAULT);


        //update new password with the authenticated user
        try {
            $authenticatedUser->update([
                'password' => $password,
                'modifydate' => date("Y-m-d"),
                'modifyuser' => $request->userid,
            ]);

            return response()->json([
                "ok" => true,
                "msg" => "Password changed successfully",
            ]);
        } catch (\Exception $th) {
            return response()->json([
                "ok" => false,
                "msg" => "An internal error occured. Reset failed",
                "error" => [
                    "msg" => $th->__toString(),
                    "fix" => "Error is explained in fix",
                ]
            ]);
        }
    }
}
