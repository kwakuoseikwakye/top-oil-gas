<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerCylinderResource;
use App\Http\Resources\CylinderResource;
use App\Http\Resources\OrderResource;
use App\Imports\CylindersImport;
use App\Models\Allocation;
use App\Models\Customer;
use App\Models\CustomerCylinder;
use App\Models\Cylinder;
use App\Models\Dispatch;
use App\Models\Exchange;
use App\Models\Log as ModelsLog;
use App\Models\User;
use Exception;
use Faker\Provider\el_CY\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Stevebauman\Location\Facades\Location;
use App\Arkesel\Arkesel as Sms;
use App\Models\CustomerLocation;
use App\Models\Payment as ModelsPayment;

class CylinderController extends Controller
{
    public function getOrders()
    {
        $data  = CustomerCylinder::select(
            'tblcustomer_cylinder.*',
            'tblcylinder_size.weight',
            'tblcylinder_size.amount',
            'tblcustomer_location.name',
            'tblcustomer_location.phone1',
            'tblcustomer_location.phone2',
            'tblcustomer_location.address',
            'tblcustomer_location.additional_info',
            'tblcustomer.custno',
            'tblcustomer.fname',
            'tblcustomer.lname',
            'tblcustomer.phone',
            'tblcylinder.owner',
            // 'tblcylinder.cylcode',
            // 'tblcylinder.weight_id',
            // 'tblcylinder.location_id',
            'tblcylinder.requested',
        )
            ->join('tblcustomer', 'tblcustomer.custno', 'tblcustomer_cylinder.custno')
            ->leftJoin('tblcylinder', 'tblcylinder.cylcode', 'tblcustomer_cylinder.cylcode')
            ->leftJoin('tblcustomer_location', 'tblcylinder.location_id', 'tblcustomer_location.id')
            ->leftJoin('tblcylinder_size', 'tblcylinder.weight_id', 'tblcylinder_size.id')
            ->orderByDesc('tblcustomer_cylinder.date_acquired')->get();

        return response()->json([
            // "data" => $data
            "data" => OrderResource::collection($data)
        ]);
    }

    public function addSingleOrder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "location_id" => "required",
                "customer" => "required",
                "delivery_mode" => "required",
                "cylcode" => "required",
                "payment_type" => "required",
            ], [
                "customer.required" => "No customer selected",
                "location_id.required" => "No location supplied",
                "delivery_mode.required" => "No location supplied",
                "cylcode.required" => "No cylinder selected",
                "payment_type.required" => "No payment type selected",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Cylinder Assignment failed. " . join(". ", $validator->errors()->all()),
                ], 422);
            }

            $cylinderExists = CustomerCylinder::where('cylcode', $request->cylcode)->first();
            if ($cylinderExists) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Cylinder already assigned"
                ], 422);
            }

            $customerCylinderExists = CustomerCylinder::where('custno', $request->customer)->exists();
            if ($customerCylinderExists) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Customer already assigned to a cylinder, please use the refill button for cylinder refill"
                ], 422);
            }

            $customerLocationExists = CustomerLocation::where('id', $request->location_id)->where('custno', $request->customer)->exists();
            if (!$customerLocationExists) {
                return response()->json([
                    "ok" => false,
                    "msg" => "This location does not belong to the customer"
                ], 422);
            }

            if ($request->delivery_mode === CustomerCylinder::PICKUP && empty($request->pickup_id)) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Pickup id is required"
                ], 422);
            }

            if ($request->order_type === CustomerCylinder::ORDER_TYPE_PICKUP_LATER && empty($request->order_type)) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Pickup date is requied"
                ], 422);
            }

            $cylinder = Cylinder::where('cylcode', $request->cylcode)->first();
            $user = User::where('userid', $request->customer)->first();

            DB::beginTransaction();
            $orderid = strtoupper(bin2hex(random_bytes(6)));
            CustomerCylinder::insert([
                "transid" => strtoupper(bin2hex(random_bytes(4))),
                "order_id" => $orderid,
                "custno" => $user->userid,
                "cylcode" => $request->cylcode,
                "date_acquired" => $request->date ?? date("Y-m-d H:i:s"),
                "location_id" => $request->location_id,
                "weight_id" => $cylinder->weight_id,
                "status" => CustomerCylinder::PENDING,
                "deleted" =>  0,
                "createdate" =>  date("Y-m-d H:i:s"),
                "createuser" =>  $user->userid,
            ]);

            Dispatch::insert([
                "transid" => strtoupper(bin2hex(random_bytes(4))),
                "order_id" => $orderid,
                "location_id" => $request->location_id,
                "pickup_location" => $request->pickup_id ?? 0,
                "deleted" =>  0,
                "status" => Dispatch::PENDING,
                "createdate" =>  date("Y-m-d H:i:s"),
                "createuser" =>  $user->userid,
            ]);

            Cylinder::where('cylcode', $request->cylcode)->update(['requested' => 1]); // Mark the cylinder as requested by someone]);

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $user->userid,
                "module" => "Cylinder",
                "action" => "Assignment",
                "activity" => "Order  {$orderid} assigned from Mobile with id successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $user->userid,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            if ($request->payment_type == "online") {
                $payLink = PaymentController::generatePaymentLink($orderid);
                if ($payLink['code'] === 200) {
                    $msg = <<<MSG
                    Dear customer,
                    Your order for a cylinder with order ID {$orderid} is successful.
                    Kindly click on the payment link below to complete your order. 
                    {$payLink['checkout_url']}
                    MSG;

                    $sms = new Sms('TOP-OIL', env('ARKESEL_SMS_API_KEY'));
                    $sms->send($user->phone, $msg);
                } else {
                    ModelsPayment::insert([
                        "transid" => strtoupper(bin2hex(random_bytes(4))),
                        "order_id" => $orderid,
                        "custno" => $request->customer,
                        "status" => ModelsPayment::PENDING,
                        "payment_mode" => "cash",
                    ]);
                }
            } else {
                ModelsPayment::insert([
                    "transid" => strtoupper(bin2hex(random_bytes(4))),
                    "order_id" => $orderid,
                    "custno" => $request->customer,
                    "status" => ModelsPayment::PENDING,
                    "payment_mode" => "cash",
                ]);
            }


            return response()->json([
                "ok" => true,
                "msg" => "cylinder assigned successfully",
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

    public function index()
    {
        $data = Cylinder::select('tblcylinder.*', 'tblcylinder_size.*')
            ->join('tblcylinder_size', 'tblcylinder_size.id', 'tblcylinder.weight_id')
            ->orderBy('tblcylinder.requested', 'asc')
            ->get();
        return response()->json([
            'data' => CylinderResource::collection($data),
        ]);
    }

    public function trash()
    {
        return response()->json([
            'data' => CylinderResource::collection(Cylinder::where("deleted", 1)
                ->orderBy("createdate", "DESC")->get()),
        ]);
    }

    public function cylinderReport($dateFrom, $dateTo)
    {
        return response()->json([
            'data' => CylinderResource::collection(Cylinder::where("deleted", 0)
                ->whereBetween('createdate', [$dateFrom, $dateTo])
                ->orderBy("createdate", "DESC")->get()),
        ]);
    }

    public function cylinderCustomer()
    {
        return response()->json([
            'data' => CustomerCylinderResource::collection(CustomerCylinder::where("deleted", 0)->with('cylinder', 'user')
                ->orderBy("createdate", "DESC")->get())
        ]);
    }

    public function customerCylinderReport($customer, $dateFrom, $dateTo)
    {
        return response()->json([
            'data' => CustomerCylinderResource::collection(CustomerCylinder::when($customer !== 'all', function ($q)  use ($customer) {
                return $q->where('custno', $customer);
            })->where("deleted", 0)->with('cylinder', 'user')
                ->whereBetween('createdate', [$dateFrom, $dateTo])
                ->orderBy("createdate", "DESC")->get())
        ]);
    }

    public function cylinderCustomerReport($cylinder, $dateFrom, $dateTo)
    {
        return response()->json([
            'data' => CustomerCylinderResource::collection(CustomerCylinder::when($cylinder !== 'all', function ($q)  use ($cylinder) {
                return $q->where('cylcode', $cylinder);
            })->where("deleted", 0)->with('cylinder', 'user')
                ->whereBetween('createdate', [$dateFrom, $dateTo])
                ->orderBy("createdate", "DESC")->get())
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "owner" => "required",
                "cylcode" => "required",
                "size" => "required",
            ], [
                // This has our own custom error messages for each validation
                "owner.required" => "No owner supplied",
                "cylcode.required" => "No cylinder number supplied",
                "size.required" => "No cylinder size supplied",

            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Cylinder Registration failed. " . join(". ", $validator->errors()->all()),
                ]);
            }

            DB::beginTransaction();
            $transid = strtoupper(bin2hex(random_bytes(4)));
            DB::table("tblcylinder")->insert([
                "transid" => $transid,
                "owner" => $request->owner,
                "barcode" => $request->barcode,
                "cylcode" => $request->cylcode,
                "notes" => $request->notes,
                "size" => $request->size,
                "weight" => $request->weight,
                "initial_amount" => $request->initial_amount,
                "deleted" =>  0,
                "createdate" =>  date("Y-m-d H:i:s"),
                "createuser" =>  $request->createuser,
            ]);

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Cylinder",
                "action" => "Register",
                "activity" => "Cylinder registered from BO with id {$request->cylcode} successfully",
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
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured during cylinder registration", [
                "errMsg" => $e->getMessage(),
                "trace" => $e->getTrace(),
            ]);

            return response()->json([
                "ok" => false,
                "msg" => "Request failed. An internal error occured",
            ]);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "transid" => "required",
                "owner" => "required",
                "cylcode" => "required",
                "size" => "required",
            ], [
                // This has our own custom error messages for each validation
                "transid.required" => "No id supplied",
                "owner.required" => "No owner supplied",
                "cylcode.required" => "No cylinder number supplied",
                "size.required" => "No cylinder size supplied",

            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Cylinder update failed. " . join(". ", $validator->errors()->all()),
                ]);
            }

            DB::beginTransaction();
            DB::table("tblcylinder")->where("transid", $request->transid)->update([
                "owner" => $request->owner,
                "barcode" => $request->barcode,
                "cylcode" => $request->cylcode,
                "size" => $request->size,
                "notes" => $request->notes,
                "weight" => $request->weight,
                "initial_amount" => $request->initial_amount,
                "modifydate" =>  date("Y-m-d H:i:s"),
                "modifyuser" =>  $request->createuser,
            ]);

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Cylinder",
                "action" => "Update",
                "activity" => "Cylinder updated from BO with id {$request->cylcode} successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Cylinder updated successfully",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured during cylinder update", [
                "errMsg" => $e->getMessage(),
                "trace" => $e->getTrace(),
            ]);

            return response()->json([
                "ok" => false,
                "msg" => "Request failed. An internal error occured",
                "errMsg" => $e->getMessage(),
                "trace" => $e->getTrace(),
            ]);
        }
    }

    public function assignCylinder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "custno" => "required",
                "cylcode" => "required",
                // "transid" => "required",
                // "weight_id" => "required",
                // "orderid" => "required",
                // "createuser" => "required",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Cylinder Assignment failed. " . join(". ", $validator->errors()->all()),
                ]);
            }

            DB::beginTransaction();
            // $transid = strtoupper(bin2hex(random_bytes(4)));
            // $cylinder = Cylinder::find($request->cylinderNo);
            $customerOrder = CustomerCylinder::where("transid", $request->transid)->first();
            CustomerCylinder::where("transid", $request->transid)->update([
                "cylcode" => $request->cylcode,
                // "weight_id" => $request->weight_id,
                "modifydate" =>  date("Y-m-d H:i:s"),
                "modifyuser" =>  $request->createuser,
            ]);
            Cylinder::where('cylcode', $request->cylcode)->update([
                'requested' => 1,
                'location_id' => $customerOrder->location_id
            ]); // Mark the cylinder as requested by someone]);


            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Cylinder",
                "action" => "Assignment",
                "activity" => "Cylinder {$request->cylcode} assigned from Back Office with id successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Assigned successfully",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured during cylinder assignment", [
                "errMsg" => $e->getMessage(),
                "trace" => $e->getTrace(),
            ]);

            return response()->json([
                "ok" => false,
                "msg" => "Request failed. An internal error occured",
            ]);
        }
    }

    public function updateAssignCylinder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "custno" => "required",
                "cylcode" => "required",
                "transid" => "required",
                "weight_id" => "required",
                "orderid" => "required",
                "createuser" => "required",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Cylinder Assignment update failed. " . join(". ", $validator->errors()->all()),
                ]);
            }

            DB::beginTransaction();
            $oldcylinder = CustomerCylinder::where('transid', $request->transid)->where('order_id', $request->order_id)->first();
            $cylinder = Cylinder::where('cylcode', $request->cylcode)->first();

            CustomerCylinder::where("transid", $request->transid)->update([
                "cylcode" => $request->cylcode,
                "modifydate" =>  date("Y-m-d H:i:s"),
                "modifyuser" =>  $request->createuser,
            ]);
            Cylinder::where('cylcode', $request->cylcode)->update([
                'requested' => 1,
            ]);

            Dispatch::where('transid', $request->transid)->where('order_id', $request->order_id)->update([
                "location_id" => $cylinder->location_id,
                "modifydate" =>  date("Y-m-d H:i:s"),
                "modifyuser" =>  $request->custno,
            ]);

            Exchange::insert([
                "transid" => strtoupper(bin2hex(random_bytes(4))),
                "custno" => $request->custno,
                "order_id" => $request->order_id,
                "cylcode_old" => $oldcylinder->cylcode,
                "cylcode_new" => $request->cylcode,
                "status" => "pending",
                "deleted" =>  0,
                "createdate" =>  date("Y-m-d H:i:s"),
                "createuser" =>  $request->custno,
            ]);

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Cylinder",
                "action" => "Assignment",
                "activity" => "Cylinder {$cylinder->cylcode} assignment updated from Back Office successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Assignment updated successfully",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured during cylinder assignment update", [
                "errMsg" => $e->getMessage(),
                "trace" => $e->getTrace(),
            ]);

            return response()->json([
                "ok" => false,
                "msg" => "Request failed. An internal error occured",
            ]);
        }
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "transid" => "required",
            "createuser" => "required",
        ], [
            "transid.required" => "No cylinder id supplied",

            "createuser.required" => "No userid supplied",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "ok" => false,
                "msg" => "Cylinder removal failed. " . join(". ", $validator->errors()->all()),
            ]);
        }

        try {
            DB::beginTransaction();
            $cylinder = Cylinder::where("transid", $request->transid)->where("deleted", 0)->first();

            if (empty($cylinder)) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Unknown code supplied",
                ]);
            }

            $updated = $cylinder->update([
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
                "module" => "Cylinder",
                "action" => "Delete Cylinder",
                "activity" => "Deleted Cylinder with id {$cylinder->transid}",
                "ipaddress" => $userIp,
                "createuser" => $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);
            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Cylinder Deleted successfully",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured deleting cylinder", [
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

    public function delete_assign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "transid" => "required",
            "createuser" => "required",
        ], [
            "transid.required" => "No cylinder id supplied",

            "createuser.required" => "No userid supplied",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "ok" => false,
                "msg" => "Assignment removal failed. " . join(". ", $validator->errors()->all()),
            ]);
        }

        try {
            DB::beginTransaction();
            $cylinder = CustomerCylinder::where("transid", $request->transid)->where("deleted", 0)->first();

            if (empty($cylinder)) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Unknown code supplied",
                ]);
            }

            $updated = $cylinder->update([
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
                "module" => "Cylinder",
                "action" => "Delete Cylinder Assigned",
                "activity" => "Deleted Cylinder assigned with id {$cylinder->transid}",
                "ipaddress" => $userIp,
                "createuser" => $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);
            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Cylinder Assignment Deleted successfully",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured deleting cylinder", [
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

    public function import()
    {
        try {
            DB::beginTransaction();
            // Excel::import(new CylindersImport, request()->file('file'));

            $barcodes = Customer::where("deleted", 0)
                ->get();

            foreach ($barcodes as $barcode) {

                DB::table("tblcylinder")->insert([
                    'transid' => bin2hex(random_bytes(4)),
                    'owner' => $barcode->fname,
                    'barcode' => $barcode->lname,
                    'cylcode' => $barcode->lname,
                    'size' => $barcode->pob,
                    'notes' => $barcode->mname,
                    'images2' => $barcode->occupation,
                    'images' => $barcode->landmark,
                    'createuser' => "admin",
                    'createdate' => date("Y-m-d H:i:s"),
                ]);

                DB::table("tblcustomer_cylinder")->insert([
                    'transid' => bin2hex(random_bytes(4)),
                    'custno' => $barcode->custno,
                    'barcode' => $barcode->lname,
                    'cylcode' => $barcode->lname,
                    'status' => "1",
                    'createuser' => "admin",
                    'createdate' => date("Y-m-d H:i:s"),
                ]);
            }

            Customer::where("deleted", 0)->update([
                "lname" => "",
                "mname" => "",
                "pob" => "",
                "occupation" => "",
                "landmark" => "",
            ]);
            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Upload successful",
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "ok" => false,
                "msg" => "There is an error in your excel file upload",
                "error" => [
                    "msg" => $th->__toString(),
                ]
            ]);
        }
    }

    public function check()
    {
        try {
            DB::beginTransaction();

            $cylinders = CustomerCylinder::where("deleted", 0)->get();

            foreach ($cylinders as $cylinder) {
                $checkB = Customer::where("deleted", 0)->where("custno", "=", $cylinder->custno)->first();
                $cylinderB = Cylinder::where("deleted", 0)->where("lname", "=", $cylinder->barcode)->first();
                if (empty($checkB) && empty($cylinderB)) {
                    Customer::insert([
                        'transid' => bin2hex(random_bytes(4)),
                        'custno' => 'CUS-' . bin2hex(random_bytes(4)),
                        'lname'     => $cylinder->barcode,
                        'fname'    => $cylinder->owner,
                        'createuser' => 'admin',
                        'createdate' => date("Y-m-d H:i:s"),
                    ]);

                    $fetchCustomer = Customer::where("fname", $cylinder->owner)->first();

                    $checkCusCyl = DB::table("tblcustomer_cylinder")
                        ->where("barcode", $fetchCustomer->barcode)
                        ->first();

                    if (empty($checkCusCyl)) {
                        DB::table("tblcustomer_cylinder")->insert([
                            'transid' => bin2hex(random_bytes(4)),
                            'custno' => $fetchCustomer->custno,
                            'barcode' => $fetchCustomer->barcode,
                            'cylcode' => $fetchCustomer->barcode,
                            'status' => "1",
                            'createuser' => "admin",
                            'createdate' => date("Y-m-d H:i:s"),
                        ]);
                    } else {
                        CustomerCylinder::where("barcode", $fetchCustomer->barcode)
                            ->update([
                                'custno' => $fetchCustomer->custno,
                                'barcode' => $fetchCustomer->barcode,
                                'cylcode' => $fetchCustomer->barcode,
                                'status' => "1",
                            ]);
                    }
                } else {
                    $checkB->update([
                        "lname" => "",
                    ]);
                }
            }
            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Upload successful",
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "ok" => false,
                "msg" => "There is an error in your excel file upload",
                "error" => [
                    "msg" => $th->__toString(),
                ]
            ]);
        }
    }

    public function away()
    {
        return response()->json([
            "msg" => "away"
        ]);
    }
}
