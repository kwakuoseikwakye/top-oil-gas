<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Enums\Status;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class UserService
{
      public function changePassword(array $request, $user)
      {
            $validator = Validator::make(
                  $request,
                  [
                        "old_password" => "required|min:8",
                        "new_password" => "required|min:8",
                  ],
                  [
                        "old_password.required" => "You have to supply your new password",
                        "old_password.min" => "Your new password must be at least 8 characters long",
                        "new_password.required" => "You have to supply your current password",
                  ]
            );

            if ($validator->fails()) {
                  return apiErrorResponse("Reset failed. " . join(" ", $validator->errors()->all()), 422);
            }

            $authenticatedUser = User::where("phone", $user->phone)
                  ->first();

            if (!Hash::check($request['old_password'], $authenticatedUser->password)) {
                  return apiErrorResponse("Sorry your current password is incorrect", 401);
            }

            $password = Hash::make($request['new_password']);

            try {
                  $authenticatedUser->update([
                        'password' => $password
                  ]);

                  return apiSuccessResponse("Password successfully changed");
            } catch (\Exception $th) {
                  return apiErrorResponse("An internal error occured", 500, $th);
            }
      }
}
