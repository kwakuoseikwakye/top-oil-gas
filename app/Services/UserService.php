<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Enums\Status;
use App\Models\Orders;
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

      public function createOrder(array $request, $user)
      {
            try {
                  $validator = Validator::make($request, [
                        "bulk_items.*.qty" => "required",
                        "bulk_items.*.weight_id" => "required",
                        "location_id" => "required",
                  ]);

                  if ($validator->fails()) {
                        return apiErrorResponse("Failed to add order(s). " . join(". ", $validator->errors()->all()), 422);
                  }

                  DB::beginTransaction();
                  foreach ($request['bulk_items'] as $item) {
                        Orders::create([
                              "customer_id" => $user->customer_id,
                              "quantity" => $item['qty'],
                              "date_acquired" => date("Y-m-d H:i:s"),
                              "location_id" => $request['location_id'],
                              "weight_id" => $item['weight_id'],
                              "status" => Status::PENDING_PAYMENT
                        ]);
                  }

                  DB::commit();

                  return apiSuccessResponse("Order successful");
            } catch (\Throwable $e) {
                  return apiErrorResponse("Internal error occured", 500, $e);
            }
      }
}
