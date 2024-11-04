<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Enums\Status;
use App\Models\CustomerLocation;
use App\Models\Location;
use App\Models\Orders;
use App\Models\Pickup;
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
                        "delivery_type" => "required|in:delivery,pickup",
                        "location_id" => "required_if:delivery_type,delivery",
                        "pickup_location_id" => "required_if:delivery_type,pickup",
                        "schedule_date_time" => "nullable",
                  ], [
                        "location_id.exists" => "Location does not exist"
                  ]);

                  if ($validator->fails()) {
                        return apiErrorResponse("Failed to add order(s). " . join(". ", $validator->errors()->all()), 422);
                  }

                  if (!empty($request['schedule_date_time']) && empty($request['pickup_location_id'])) {
                        return apiErrorResponse('For scheduled order pickup location is required', 422);
                  }

                  if ($request['delivery_type'] == 'pickup') {
                        $pickup = Pickup::where('id', $request['pickup_location_id'])->exists();
                        if (!$pickup) {
                              return apiErrorResponse('The selected pickup location id is invalid.', 422);
                        }
                  }

                  if ($request['delivery_type'] == 'delivery') {
                        $location = CustomerLocation::where('id', $request['location_id'])->exists();
                        if (!$location) {
                              return apiErrorResponse('The selected location id is invalid.', 422);
                        }
                  }

                  if (!empty($request['pickup_location_id']) && !empty($request['location_id'])) {
                        return apiErrorResponse('Item can only be delivered to one location', 422);
                  }

                  $orderNumber = 'ord_' . substr(hash('sha256', uniqid('', true)), 0, 24);

                  // DB::beginTransaction();
                  foreach ($request['bulk_items'] as $item) {
                        $order = Orders::create([
                              "customer_id" => $user->customer_id,
                              "order_number" => $orderNumber,
                              "quantity" => $item['qty'],
                              "date_acquired" => date("Y-m-d H:i:s"),
                              "location_id" => $request['location_id'],
                              "pickup_location_id" => $request['pickup_location_id'],
                              "schedule_date_time" => $request['schedule_date_time'],
                              "weight_id" => $item['weight_id'],
                              "status" => Status::PENDING_PAYMENT
                        ]);
                  }

                  // DB::commit();
                  $data = [
                        "order_number" => $order->order_number,
                  ];

                  return apiSuccessResponse("Order successful", 201, $data);
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

      public function deleteLocation($location_id, $user)
      {
            $location = CustomerLocation::find($location_id);

            if (!$location) {
                  return apiErrorResponse("Location not found.", 404);
            }

            if ($location->customer_id != $user->customer_id) {
                  return apiErrorResponse("Unauthorized access to the location.", 403);
            }

            try {
                  $location->delete();

                  return apiSuccessResponse("Location deleted successfully");
            } catch (\Exception $th) {
                  return apiErrorResponse("An internal error occurred", 500, $th);
            }
      }

      public function uploadFile(array $data, $user)
      {
            $validator = Validator::make($data, [
                  "id_type" => "required",
                  "id_no" => "required",
                  "id_link" => "required",
            ]);

            if ($validator->fails()) {
                  return apiErrorResponse("Uploading file failed. " . join(". ", $validator->errors()->all()), 422);
            }

            try {
                  $filePath = $data['id_link']->store('images', 'public');
                  Customer::where('id', $user->customer_id)->update([
                        "id_type" => $data['id_type'] ?? null,
                        "id_no" => $data['id_no'] ?? null,
                        "id_link" => $data['id_link'] ? $filePath : null,
                  ]);

                  return apiSuccessResponse('File uploaded successfully');
            } catch (\Throwable $e) {
                  return apiErrorResponse('Internal error occurred', 500, $e);
            }
      }
}
