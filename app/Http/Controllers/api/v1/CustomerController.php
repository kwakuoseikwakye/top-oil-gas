<?php

namespace App\Http\Controllers\api\v1;

use App\Arkesel\Arkesel as ASms;
use App\Http\Controllers\Controller;
use App\Models\Log as ModelsLog;
use App\Models\Customer;
use App\Models\CustomerCylinder;
use App\Models\CustomerLocation;
use App\Models\Cylinder;
use App\Models\CylinderSize;
use App\Models\Dispatch;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isNull;

class CustomerController extends Controller
{
    public static function extractToken(Request $request)
    {
        $authorizationHeader = $request->header('Authorization');
        if (Str::startsWith($authorizationHeader, 'Bearer ')) {
            return Str::substr($authorizationHeader, 7);
        }
        return null;
    }

    private function getAuthenticatedCustomer(Request $request)
    {
        $token = $this->extractToken($request);

        if ($token) {
            $user = User::where('remember_token', $token)->first();
            return $user->userid;
        } else {
            return null;
        }
    }

    public function getPickupStations(Request $request)
    {
        return response()->json([
            "status" => true,
            "message" => "Request successful",
            "data" => DB::table("tblpickup")->get()
        ]);
    }

    public function getOrders(Request $request)
    {
        $token = $this->extractToken($request);

        if (!empty($token)) {
            $user = User::where('remember_token', $token)->first();
            if (empty($user)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized - Token not provided or invalid'
                ], 401);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized - Token not provided or invalid'
            ], 401);
        }
        return response()->json([
            "status" => true,
            "message" => "Request successful",
            "data" => CustomerCylinder::with(['cylinder', 'cylinder.cylinderWeight'])->where('tblcustomer_cylinder.custno', $user->userid)->get()
        ]);
    }

    public function getCustomerCylinders(Request $request)
    {
        try {
            $token = $this->extractToken($request);

            if (!empty($token)) {
                $user = User::where('remember_token', $token)->first();
                if (empty($user)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthorized - Token not provided or invalid'
                    ], 401);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized - Token not provided or invalid'
                ], 401);
            }
            return response()->json([
                "status" => true,
                "message" => "Request successful",
                "data" => CustomerLocation::select("tblcylinder.*", "tblcylinder_size.*")
                    ->join("tblcylinder", "tblcylinder.location_id", "tblcustomer_location.id")
                    ->join("tblcylinder_size", "tblcylinder_size.id", "tblcylinder.weight_id")
                    ->where("tblcustomer_location.custno", $user->userid)
                    ->get()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Request failed. An internal error occured",
                "errMsg" => $e->getMessage(),
            ]);
        }
    }

    public function getLocation(Request $request)
    {
        $token = $this->extractToken($request);

        if (!empty($token)) {
            $user = User::where('remember_token', $token)->first();
            if (empty($user)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized - Token not provided or invalid'
                ], 401);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized - Token not provided or invalid'
            ], 401);
        }
        return response()->json([
            "status" => true,
            "message" => "Request successful",
            "data" => CustomerLocation::with(['cylinders', 'cylinders.cylinderWeight'])->where('tblcustomer_location.custno', $user->userid)->get()
        ]);
    }

    public function getDispatch(Request $request, $orderid)
    {
        try {
            $token = $this->extractToken($request);

            if (!empty($token)) {
                $user = User::where('remember_token', $token)->first();
                if (empty($user)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthorized - Token not provided or invalid'
                    ], 401);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized - Token not provided or invalid'
                ], 401);
            }
            $dispatch = Dispatch::where('order_id', $orderid)->first();
            if (empty($dispatch)) {
                $customerDispatch = null;
                return response()->json([
                    "status" => true,
                    "message" => "Request successful",
                    "data" => $customerDispatch
                ]);
            }
            if ($dispatch->pickup_location == 0) {
                $customerDispatch =  Dispatch::select('tbldispatch.*', 'tblcustomer_location.*')
                    ->join('tblcustomer_location', 'tblcustomer_location.id', 'tbldispatch.location_id')
                    ->where('tbldispatch.order_id', $orderid)
                    // ->where('tbldispatch.createuser', $user->userid)
                    ->get();
            } else {
                $customerDispatch = Dispatch::select('tbldispatch.*', 'tblpickup.*')
                    ->join('tblpickup', 'tblpickup.id', 'tbldispatch.pickup_location')
                    ->where('tbldispatch.order_id', $orderid)->get();
            }
            return response()->json([
                "status" => true,
                "message" => "Request successful",
                "data" => $customerDispatch
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Request failed. An internal error occured",
                "errMsg" => $e->getMessage(),
                "trace" => $e->getTrace(),
            ], 500);
        }
    }

    public function addLocation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "name" => "required",
                "custno" => "required",
                "address" => "required",
                "default" => "required",
                "lat" => "required",
                "long" => "required",
                "phone1" => "required|numeric",
                // "cylcode" => "required|unique:tblcustomer_location,cylcode",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => false,
                    "message" => "Adding location failed. " . join(". ", $validator->errors()->all()),
                ]);
            }

            $token = $this->extractToken($request);

            if (!empty($token)) {
                $user = User::where('remember_token', $token)->first();
                if (empty($user)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthorized - Token not provided or invalid'
                    ], 401);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized - Token not provided or invalid'
                ], 401);
            }

            // $checkPhone = CustomerLocation::where('custno',$request->custno)->where

            DB::beginTransaction();
            DB::table("tblcustomer_location")->insert([
                "name" => $request->name,
                "custno" => $request->custno,
                "phone1" => $request->phone1,
                "phone2" => $request->phone2,
                "address" => $request->address,
                "additional_info" => $request->additional_info,
                "default" => $request->default,
                "long" => $request->long,
                "lat" => $request->lat,
            ]);

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->custno,
                "module" => "Customer",
                "action" => "Add",
                "activity" => "Customer added locaiton from Mobile with id {$request->custno} successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $request->custno,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();
            return response()->json([
                "status" => true,
                "message" => "Location added successful",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("An error occured during adding location", [
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

    public function deleteLocation($id)
    {
        DB::table("tblcustomer_location")->where("id", $id)->delete();

        return response()->json([
            "status" => true,
            "message" => "Location deleted"
        ]);
    }

    public function updateLocation(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "name" => "required",
                "address" => "required",
                "lat" => "required",
                "long" => "required",
                "phone1" => "required|numeric",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => false,
                    "message" => "Updating location failed. " . join(". ", $validator->errors()->all()),
                ]);
            }

            $token = $this->extractToken($request);

            if (!empty($token)) {
                $user = User::where('remember_token', $token)->first();
                if (empty($user)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthorized - Token not provided or invalid'
                    ], 401);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized - Token not provided or invalid'
                ], 401);
            }

            DB::beginTransaction();
            DB::table("tblcustomer_location")->where("id", $id)->update([
                "name" => $request->name,
                "phone1" => $request->phone1,
                "phone2" => $request->phone2,
                "address" => $request->address,
                "additional_info" => $request->additional_info,
                "long" => $request->long,
                "lat" => $request->lat,
            ]);

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $user->userid,
                "module" => "Customer",
                "action" => "Update",
                "activity" => "Customer updated locaiton from Mobile with id {$user->userid} successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $user->userid,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();
            return response()->json([
                "status" => true,
                "message" => "Location updated successful",
                "data" => DB::table("tblcustomer_location")->where("custno", $user->userid)->where("id", $id)->first()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("An error occured during updating location", [
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

    public function addCart(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "cart_items.*.cylcode" => "required",
                "cart_items.*.qty" => "required",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => false,
                    "message" => "Adding cart failed. " . join(". ", $validator->errors()->all()),
                ], 422);
            }

            DB::beginTransaction();
            $custno = $this->getAuthenticatedCustomer($request);
            foreach ($request->cart_items as $item) {
                DB::table("tblcart")->insert([
                    "cylcode" => $item['cylcode'],
                    "qty" => $item['qty'],
                    "custno" => $custno,
                ]);
            }

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $custno,
                "module" => "Customer",
                "action" => "Add",
                "activity" => "Customer added cart from Mobile with id {$custno} successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $custno,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();
            return response()->json([
                "status" => true,
                "message" => "Cart added successful",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured during adding location", [
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

    public function bulkOrder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "bulk_items.*.qty" => "required",
                "bulk_items.*.weight_id" => "required",
                "location_id" => "required",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => false,
                    "message" => "bulk order failed. " . join(". ", $validator->errors()->all()),
                ], 422);
            }
            $token = $this->extractToken($request);

            if (!empty($token)) {
                $user = User::where('remember_token', $token)->first();
                if (empty($user)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthorized - Token not provided or invalid'
                    ], 401);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized - Token not provided or invalid'
                ], 401);
            }

            DB::beginTransaction();
            $orderid = strtoupper(bin2hex(random_bytes(4)));
            foreach ($request->bulk_items as $item) {
                DB::table("tblcustomer_cylinder")->insert([
                    "transid" => strtoupper(bin2hex(random_bytes(4))),
                    "order_id" => $orderid,
                    "custno" => $user->userid,
                    "quantity" => $item['qty'],
                    "date_acquired" => date("Y-m-d"),
                    "location_id" => $request->location_id,
                    "weight_id" => $item['weight_id'],
                    "status" => 0,
                    "deleted" =>  0,
                    "createdate" =>  date("Y-m-d H:i:s"),
                    "createuser" =>  $user->userid,
                ]);
            }

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $user->userid,
                "module" => "Customer",
                "action" => "Add",
                "activity" => "Customer ordered bulk from Mobile with id {$user->userid} successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $user->userid,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();
            return response()->json([
                "status" => true,
                "message" => "Order successful",
                "data" => $orderid
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured during adding order", [
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

    public function addOrders(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "date" => "required",
                "location_id" => "required",
                "weight_id" => "required",
            ], [
                "date.required" => "No date supplied",
                "weight_id.required" => "No weight supplied",
                "location_id.required" => "No location supplied",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => false,
                    "message" => "Adding customer orders failed. " . join(". ", $validator->errors()->all()),
                ], 422);
            }

            $token = $this->extractToken($request);

            if (!empty($token)) {
                $user = User::where('remember_token', $token)->first();
                if (empty($user)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthorized - Token not provided or invalid'
                    ], 401);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized - Token not provided or invalid'
                ], 401);
            }


            DB::beginTransaction();
            $orderid = strtoupper(bin2hex(random_bytes(6)));
            DB::table("tblcustomer_cylinder")->insert([
                "transid" => strtoupper(bin2hex(random_bytes(4))),
                "custno" => $user->userid,
                "order_id" => $orderid,
                // "cylcode" => $cylinder->cylcode,
                "quantity" => "1",
                "date_acquired" => $request->date,
                "location_id" => $request->location_id,
                "weight_id" => $request->weight_id,
                "status" => 0,
                "deleted" =>  0,
                "createdate" =>  date("Y-m-d H:i:s"),
                "createuser" =>  $user->userid,
            ]);

            // Cylinder::where('cylcode', $cylinder->cylcode)->update(['requested' => 1]); // Mark the cylinder as requested by someone]);

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $user->userid,
                "module" => "Cylinder",
                "action" => "Assignment",
                "activity" => "Cylinder assigned from Mobile with id successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $user->userid,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Ordered successfully",
                "data" => CustomerCylinder::where('order_id', $orderid)->first()
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured during cylinder assignment", [
                "errMsg" => $e->getMessage(),
                "trace" => $e->getTrace(),
            ]);

            return response()->json([
                "status" => false,
                "message" => "Request failed. An internal error occured",
                "errMsg" => $e->getMessage(),
                "trace" => $e->getTrace(),
            ], 500);
        }
    }

    public function purchaseNow(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "location_id" => "required",
                "weight_id" => "required",
            ], [
                "weight_id.required" => "No weight supplied",
                "location_id.required" => "No location supplied",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => false,
                    "message" => "Purchasing customer orders failed. " . join(". ", $validator->errors()->all()),
                ], 422);
            }

            $token = $this->extractToken($request);

            if (!is_null($token)) {
                $user = User::where('remember_token', $token)->first();
                if (empty($user)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthorized - Token not provided or invalid'
                    ], 401);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized - Token not provided or invalid'
                ], 401);
            }

            DB::beginTransaction();
            $orderid = strtoupper(bin2hex(random_bytes(6)));
            DB::table("tblcustomer_cylinder")->insert([
                "transid" => strtoupper(bin2hex(random_bytes(4))),
                "order_id" => $orderid,
                "custno" => $user->userid,
                // "cylcode" => $cylinder->cylcode,
                "quantity" => "1",
                "date_acquired" => date("Y-m-d"),
                "weight_id" => $request->weight_id,
                "location_id" => $request->location_id,
                "status" => 0,
                "deleted" =>  0,
                "createdate" =>  date("Y-m-d H:i:s"),
                "createuser" =>  $user->userid,
            ]);

            // Cylinder::where('cylcode', $cylinder->cylcode)->update(['requested' => 1]); // Mark the cylinder as requested by someone]);

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $user->userid,
                "module" => "Cylinder",
                "action" => "Assignment",
                "activity" => "Cylinder ordered from Mobile with id successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $user->userid,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Ordered successfully",
                "data" => CustomerCylinder::where('order_id', $orderid)->first()
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured during cylinder assignment", [
                "errMsg" => $e->getMessage(),
                "trace" => $e->getTrace(),
            ]);

            return response()->json([
                "status" => false,
                "message" => "Request failed. An internal error occured",
                "errMsg" => $e->getMessage(),
                "trace" => $e->getTrace(),
            ], 500);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $token = $this->extractToken($request);

        if ($token) {
            $user = User::where('remember_token', $token)->first();

            if ($user) {
                $cust = Customer::where('tblcustomer.custno', $user->userid)->get();
                return response()->json([
                    'status' => true,
                    'message' => 'Request Successful',
                    'data' => $cust
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized - User not found'
                ], 401);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized - Token not provided or invalid'
            ], 401);
        }
    }

    public function trash()
    {
        return response()->json([
            'ok' => true,
            'msg' => 'Request successful',
            'data' => Customer::where("deleted", 1)->with('cylinders', 'user')->orderBy("createdate", "DESC")->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "title" => "required",
                "firstName" => "required",
                "lastName" => "required",
                "phoneNumber" => "required|numeric|unique:tblcustomer,phone",
                // "email" => "required|email|unique:tbluser,email",
                // "idType" => "required",
                // "idNumber" => "required",
                // "idFileLink" => "required",
                // "homeAddress" => "required",
                // "region" => "required",
                // "town" => "required",
                // "streetName" => "required",
                // "landmark" => "required",
                // "gpsaddress" => "required",
                // "owner" => "required",
                // "cylcode" => "required",
                // "size" => "required",
            ], [
                // This has our own custom error messages for each validation
                "title.required" => "No title supplied",

                "firstName.required" => "No first name supplied",
                "lastName.required" => "No last name supplied",

                // Phone error messages
                "phoneNumber.required" => "No phone number supplied",
                "phoneNumber.numeric" => "Phone number supplied [{$request->phoneNumber}] must contain only numbers",
                "phoneNumber.unique" => "Phone number already taken",

                // Gender error messages
                // "gender.required" => "Your gender was not specified",
                // "gender.max" => "Invalid value for gender: expected one of ['m','f'] but got [{$request->gender}]",

                // Email error messages
                // "email.email" => "The supplied email [{$request->email}] is not a valid email",
                // "email.required" => "No email supplied",
                // "email.unique" => "Email already taken",

                // ID error messages
                // "idType.required" => "ID Type is required",
                // "idNumber.required" => "ID number is required",
                // "idFileLink.required" => "Upload ID",

                // Address error messages
                // "homeAddress.required" => "Home Address is required",
                // "region.required" => "Region is required",
                // "town.required" => "Town is required",
                // "streetName.required" => "Streetname is required",
                // "landmark.required" => "Landmark is required",
                // "gpsaddress.required" => "GPS Address is required",

                // "owner.required" => "No owner supplied",
                // "cylcode.required" => "No cylinder number supplied",
                // "size.required" => "No cylinder size supplied",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Registration failed. " . join(". ", $validator->errors()->all()),
                ]);
            }

            DB::beginTransaction();
            $transid = strtoupper(bin2hex(random_bytes(4)));
            DB::table("tbluser")->insert([
                "transid" => $transid,
                "userid" => 'CUS-' . $transid,
                "fname" => $request->firstName,
                "lname" => $request->lastName,
                "username" => strtolower($transid),
                "usertype" => "customer",
                "password" =>  Hash::make(12345678),
                "phone" => empty($request->phoneNumber) ? '' : $request->phoneNumber,
                "email" => empty($request->email) ? '' : $request->email,
                "picture" => $request->picture,
                "deleted" =>  0,
                "createdate" =>  date("Y-m-d H:i:s"),
                "createuser" =>  $request->createuser,
            ]);

            DB::table('tblcustomer')->insert([
                "transid" => $transid,
                "custno" => 'CUS-' . $transid,
                "title" => $request->title,
                "fname" => strtoupper($request->firstName),
                "lname" => strtoupper($request->lastName),
                "mname" => empty($request->middleName) ? '' : strtoupper($request->middleName),
                "dob" => $request->dateOfBirth,
                "pob" => $request->placeOfBirth,
                "marital_status" => $request->maritalStatus,
                "occupation" => $request->occupation,
                "home_address" => $request->homeAddress,
                "region" => $request->region,
                "town" => $request->town,
                "streetname" => $request->streetName,
                "landmark" => $request->landmark,
                "gpsaddress" => $request->gpsaddress,
                "phone" => $request->phoneNumber,
                "email" => empty($request->email) ? '' : $request->email,
                "id_type" => $request->idType,
                "id_no" => $request->idNumber,
                "id_link" => $request->idFileLink,
                "gender" => strtoupper($request->gender),
                "picture" => $request->picture,
                "longitude" => $request->longitude,
                "latitude" => $request->latitude,
                "createdate" =>  date('Y-m-d H:i:s'),
                "createuser" => $request->createuser,
                "deleted" => 0,
            ]);

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            if (!empty($request->phoneNumber)) {
                $msg = <<<MSG
             Dear Customer, thank you for signing up for Petrocell cylinder services.
            MSG;

                $sms = new ASms('PETROCELL', env('ARKESEL_SMS_API_KEY'));
                $sms->send($request->phoneNumber, $msg);
            }

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Customer",
                "action" => "Add",
                "activity" => "Customer registered from Mobile with id CUS-{$transid} successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Registration successful",
                "custno" => 'CUS-' . $transid,
            ]);
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "custno" => "required",
                "title" => "required",
                "firstName" => "required",
                "lastName" => "required",
                "phoneNumber" => "required|numeric",
                "email" => "required|email",
                "idType" => "required",
                "idNumber" => "required",
                "idFileLink" => "required",
                "homeAddress" => "required",
                "region" => "required",
                "town" => "required",
                "streetName" => "required",
                "landmark" => "required",
                "gpsaddress" => "required",
            ], [
                "custno.required" => "No customer number supplied",

                // This has our own custom error messages for each validation
                "title.required" => "No title supplied",

                "firstName.required" => "No first name supplied",
                "lastName.required" => "No last name supplied",

                // Phone error messages
                "phoneNumber.required" => "No phone number supplied",
                "phoneNumber.numeric" => "Phone number supplied [{$request->phoneNumber}] must contain only numbers",

                // Gender error messages
                "gender.required" => "Your gender was not specified",
                "gender.max" => "Invalid value for gender: expected one of ['m','f'] but got [{$request->gender}]",

                // Email error messages
                "email.email" => "The supplied email [{$request->email}] is not a valid email",
                "email.required" => "No email supplied",

                // ID error messages
                "idType.required" => "ID Type is required",
                "idNumber.required" => "ID number is required",
                "idFileLink.required" => "Upload ID",

                // Address error messages
                "homeAddress.required" => "Home Address is required",
                "region.required" => "Region is required",
                "town.required" => "Town is required",
                "streetName.required" => "Streetname is required",
                "landmark.required" => "Landmark is required",
                "gpsaddress.required" => "GPS Address is required",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Customer profile update failed. " . join(". ", $validator->errors()->all()),
                ]);
            }

            DB::beginTransaction();
            DB::table("tbluser")->where("userid", $request->custno)->update([
                "fname" => $request->firstName,
                "lname" => $request->lastName,
                "phone" => empty($request->phoneNumber) ? '' : $request->phoneNumber,
                "email" => $request->email,
                "modifydate" =>  date("Y-m-d H:i:s"),
                "modifyuser" =>  $request->modifyuser,
            ]);


            DB::table('tblcustomer')->where("custno", $request->custno)->update([
                "title" => $request->title,
                "fname" => strtoupper($request->firstName),
                "lname" => strtoupper($request->lastName),
                "mname" => strtoupper($request->middleName),
                "dob" => $request->dateOfBirth,
                "pob" => $request->placeOfBirth,
                "marital_status" => $request->maritalStatus,
                "occupation" => $request->occupation,
                "home_address" => $request->homeAddress,
                "region" => $request->region,
                "town" => $request->town,
                "streetname" => $request->streetName,
                "landmark" => $request->landmark,
                "gpsaddress" => $request->gpsaddress,
                "phone" => $request->phoneNumber,
                "email" => $request->email,
                "id_type" => $request->idType,
                "id_no" => $request->idNumber,
                "id_link" => $request->idFileLink,
                "gender" => strtoupper($request->gender),
                "picture" => $request->picture,
                "longitude" => $request->longitude,
                "latitude" => $request->latitude,
                "modifydate" =>  date('Y-m-d H:i:s'),
                "modifyuser" => $request->modifyuser,
            ]);

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->modifyuser,
                "module" => "Customer",
                "action" => "Update",
                "activity" => "Customer profile updated from Mobile with id {$request->custno} successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $request->modifyuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Customer profile updated successfully",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured during customer profile update", [
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "transid" => "required",
            "createuser" => "required",
        ], [
            "transid.required" => "No customer id supplied",

            "createuser.required" => "No userid supplied",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "ok" => false,
                "msg" => "Customer removal failed. " . join(". ", $validator->errors()->all()),
            ]);
        }

        try {
            DB::beginTransaction();
            $user = User::where("transid", $request->transid)->where("deleted", 0)->first();

            if (empty($user)) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Unknown code supplied",
                ]);
            }

            $user->update([
                "deleted" => 1,
            ]);

            $customer = Customer::where("transid", $request->transid)->where("deleted", 0)->first();

            if (empty($customer)) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Unknown code supplied",
                ]);
            }

            $updated = $customer->update([
                "deleted" => 1,
            ]);

            if (!$updated) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Delete failed",
                ]);
            }

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid,
                "username" => $request->createuser,
                "module" => "Customer",
                "action" => "Delete Customer",
                "activity" => "Deleted Customer with id {$customer->custno}",
                "ipaddress" => $userIp,
                "createuser" => $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);
            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Customer Deleted successfully",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured deleting customer", [
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

    public function restore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "transid" => "required",
            "createuser" => "required",
        ], [
            "transid.required" => "No customer id supplied",

            "createuser.required" => "No userid supplied",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "ok" => false,
                "msg" => "Customer removal failed. " . join(". ", $validator->errors()->all()),
            ]);
        }

        try {
            DB::beginTransaction();
            $user = User::where("transid", $request->transid)->where("deleted", 1)->first();

            if (empty($user)) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Unknown code supplied",
                ]);
            }

            $user->update([
                "deleted" => 0,
            ]);

            $customer = Customer::where("transid", $request->transid)->where("deleted", 1)->first();

            if (empty($customer)) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Unknown code supplied",
                ]);
            }

            $updated = $customer->update([
                "deleted" => 0,
            ]);

            if (!$updated) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Delete failed",
                ]);
            }

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid,
                "username" => $request->createuser,
                "module" => "Customer",
                "action" => "Restore Customer",
                "activity" => "Restored Customer with id {$customer->custno}",
                "ipaddress" => $userIp,
                "createuser" => $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);
            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Customer Restored successfully",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured restore customer", [
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
}
