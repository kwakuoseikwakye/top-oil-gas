<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Enums\Status;
use App\Models\CustomerLocation;
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
                        "bulk_items.*.weight_id" => "required|exists:cylinder_weights,id",
                        "location_id" => "required|exists:customer_locations,id",
                  ]);

                  if ($validator->fails()) {
                        return apiErrorResponse("Failed to add order(s). " . join(". ", $validator->errors()->all()), 422);
                  }

                  DB::beginTransaction();
                  foreach ($request['bulk_items'] as $item) {
                        $order = Orders::create([
                              "customer_id" => $user->customer_id,
                              "quantity" => $item['qty'],
                              "date_acquired" => date("Y-m-d H:i:s"),
                              "location_id" => $request['location_id'],
                              "weight_id" => $item['weight_id'],
                              "status" => Status::PENDING_PAYMENT
                        ]);
                  }

                  DB::commit();

                  return apiSuccessResponse("Order successful", 201, $order);
            } catch (\Throwable $e) {
                  return apiErrorResponse("Internal error occured", 500, $e);
            }
      }

      public function createLocation(array $request, $user)
      {
            $validator = Validator::make(
                  $request,
                  [
                        "name" => "required|min:10",
                        "phone1" => "required|min:10",
                        "address" => "required",
                        "phone2" => "nullable",
                        "longitude" => "nullable",
                        "latitude" => "nullable",
                        "additional_info" => "nullable",
                  ]
            );

            if ($validator->fails()) {
                  return apiErrorResponse("Adding location failed. " . join(" ", $validator->errors()->all()), 422);
            }

            try {
                  $location = CustomerLocation::create($validator->validated() + ['customer_id' => $user->customer_id]);

                  return apiSuccessResponse("Location added successfully", 201, $location);
            } catch (\Exception $th) {
                  return apiErrorResponse("An internal error occured", 500, $th);
            }
      }

      public function updateLocation(array $request, $location_id, $user)
      {
            $validator = Validator::make(
                  $request,
                  [
                        "name" => "nullable",
                        "phone1" => "nullable|min:10",
                        "address" => "nullable",
                        "phone2" => "nullable",
                        "longitude" => "nullable",
                        "latitude" => "nullable",
                        "additional_info" => "nullable",
                  ]
            );

            if ($validator->fails()) {
                  return apiErrorResponse("Updating location failed. " . join(" ", $validator->errors()->all()), 422);
            }

            $location = CustomerLocation::find($location_id);

            if (!$location) {
                  return apiErrorResponse("Location not found.", 404);
            }

            if ($location->customer_id != $user->customer_id) {
                  return apiErrorResponse("Unauthorized access to the location.", 403);
            }

            try {
                  $location->update($validator->validated());

                  return apiSuccessResponse("Location updated successfully", 202, $location);
            } catch (\Exception $th) {
                  return apiErrorResponse("An internal error occurred", 500, $th);
            }
      }

      public function setDefaultLocation(array $request, $id, $user)
      {
            $validator = Validator::make(
                  $request,
                  [
                        "default" => "required|boolean",
                  ]
            );

            if ($validator->fails()) {
                  return apiErrorResponse("Adding default location failed. " . join(" ", $validator->errors()->all()), 422);
            }

            if ($request['default'] === false) {
                  return apiErrorResponse("Adding default location failed. The default field accept only true values", 422);
            }

            try {
                  $location = CustomerLocation::find($id);

                  if (!$location) {
                        return apiErrorResponse("Location not found.", 404);
                  }

                  $customerId = $user->customer_id;

                  if ($request['default'] === true) {
                        CustomerLocation::where('customer_id', $customerId)
                              ->where('default', true)
                              ->update(['default' => false]);
                  }

                  $location->update($validator->validated());
                  CustomerLocation::where('id', $id)->update($validator->validated());

                  return apiSuccessResponse("Default location set successfully", 202, $location);
            } catch (\Exception $th) {
                  return apiErrorResponse("An internal error occured", 500, $th);
            }
      }
}
