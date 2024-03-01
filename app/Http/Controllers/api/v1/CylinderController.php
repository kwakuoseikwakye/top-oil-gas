<?php

namespace App\Http\Controllers\api\v1;

use App\Arkesel\Arkesel as ASms;
use App\Http\Controllers\Controller;
use App\Models\Log as ModelsLog;
use App\Models\Cylinder;
use App\Models\Customer;
use App\Models\CustomerCylinder;
use App\Models\Condition;
use App\Models\Exchange;
use App\Models\Owner;
use App\Models\PaymentMode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Str;

class CylinderController extends Controller
{
    protected function extractToken(Request $request)
    {
        $authorizationHeader = $request->header('Authorization');
        if (Str::startsWith($authorizationHeader, 'Bearer ')) {
            return Str::substr($authorizationHeader, 7);
        }
        return null;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'ok' => true,
            'msg' => 'Request successful',
            'data' => Cylinder::select('tblcylinder.*', 'tblcylinder_size.*')
                ->join('tblcylinder_size', 'tblcylinder_size.id', 'tblcylinder.weight_id')->get()
        ]);
    }

    public function fetchCylinderWeight()
    {
        return response()->json([
            'ok' => true,
            'msg' => 'Request successful',
            'data' => DB::table('tblcylinder_size')->get()
        ]);
    }

    public function cylinderConditions()
    {
        return response()->json([
            'ok' => true,
            'msg' => 'Request successful',
            'data' => Condition::where("deleted", 0)->orderBy("createdate", "DESC")->get(),
        ]);
    }

    public function cylinderCapacity()
    {
        return response()->json([
            'ok' => true,
            'msg' => 'Request successful',
            'data' => DB::table("tblcylinder_size")->where("deleted", 0)->orderBy("createdate", "DESC")->get(),
        ]);
    }

    public function dropdowns()
    {
        return response()->json([
            'ok' => true,
            'msg' => 'Request successful',
            "dropdowns" => [
                "capacity" => DB::table("tblcylinder_size")->where("deleted", 0)->orderBy("createdate", "DESC")->get(),
                "owner" => Owner::where("deleted", 0)->orderBy("createdate", "DESC")->get(),
                'payment_types' => PaymentMode::where("deleted", 0)->orderBy("createdate", "DESC")->get(),
                'warehouses' => DB::table("tblwarehouse")->select('wcode', 'wname')->where("deleted", 0)->orderBy("createdate", "DESC")->get(),
                'location' => DB::table("tblroute")->select('route_code', 'route_description')->where("deleted", 0)->orderBy("createdate", "DESC")->get(),
                "roles" => DB::table("tblrole")->where("deleted", 0)->get(),
            ]
        ]);
    }

    public function cylinderOwner()
    {
        return response()->json([
            'ok' => true,
            'msg' => 'Request successful',
            'data' => Owner::where("deleted", 0)->orderBy("createdate", "DESC")->get(),
        ]);
    }

    public function paymentMode()
    {
        return response()->json([
            'ok' => true,
            'msg' => 'Request successful',
            'data' => PaymentMode::where("deleted", 0)->orderBy("createdate", "DESC")->get(),
        ]);
    }


    public function search($seachType, $keyword)
    {
        switch ($seachType) {
            case 0:
                $data = Customer::where("deleted", 0)->where('phone', $keyword)->with('cylinders', 'cylinders.cylinder', 'user')->orderBy("createdate", "DESC")->get();
                break;

            case 1:
                $data = CustomerCylinder::where("deleted", 0)->where('cylcode', $keyword)->with('cylinder', 'user')->orderBy("createdate", "DESC")->get();
                break;

            case 2:
                $data = Customer::where("deleted", 0)->where('id_no', $keyword)->with('cylinders', 'cylinders.cylinder', 'user')->orderBy("createdate", "DESC")->get();
                break;

            case 3:
                $data = Customer::where("deleted", 0)->where('custno', $keyword)->with('cylinders', 'cylinders.cylinder', 'user')->orderBy("createdate", "DESC")->get();
                break;

            case 4:
                $data = CustomerCylinder::where("deleted", 0)->where('barcode', $keyword)->with('cylinder', 'user')->orderBy("createdate", "DESC")->get();
                break;

            case 5:
                $data = Cylinder::where("deleted", 0)->where('cylcode', $keyword)->orWhere('barcode', $keyword)->with('customers')->orderBy("createdate", "DESC")->get();
                break;


            default:
                $data = Customer::where("deleted", 0)->where('custno', $keyword)->with('cylinders', 'user')->orderBy("createdate", "DESC")->get();
                break;
        }

        return response()->json([
            'ok' => true,
            'msg' => 'Request successful',
            'data' => $data,
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
                "size" => $request->size,
                "weight" => $request->weight,
                "initial_amount" => $request->initial_amount,
                "images" => $request->imageJson,
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
                "activity" => "Cylinder registered from Mobile with id {$request->cylcode} successfully",
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

    public function assignSingleCylinder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                // "date" => "required",
                "location_id" => "required",
                "weight_id" => "required",
            ], [
                // "date.required" => "No date supplied",
                "weight_id.required" => "No weight ID supplied",
                "location_id.required" => "No location supplied",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => false,
                    "message" => "Cylinder Assignment failed. " . join(". ", $validator->errors()->all()),
                ], 422);
            }

            $token = $this->extractToken($request);

            if ($token) {
                $user = User::where('remember_token', $token)->first();
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized - Token not provided or invalid'
                ], 401);
            }

            $cylinder = Cylinder::where('weight_id', $request->weight_id)->where('requested', 0)->first()->cylcode;

            if (empty($cylinder)) {
                return response()->json(['status' => false, 'message' => 'There are no available cylinders'], 406);
            }

            $cylinderExists = CustomerCylinder::where("cylcode", $cylinder)->exists();
            if ($cylinderExists) {
                return response()->json(['status' => false, 'message' => 'Cylinder already assigned to a customer'], 406);
            }

            DB::beginTransaction();
            $orderid = strtoupper(bin2hex(random_bytes(6)));
            Cylinder::where("cylcode", $cylinder)->update([
                "location_id" => $request->location_id
            ]);
            DB::table("tblcustomer_cylinder")->insert([
                "transid" => strtoupper(bin2hex(random_bytes(4))),
                "order_id" => $orderid,
                "custno" => $user->userid,
                "cylcode" => $cylinder,
                "date_acquired" => $request->date ?? date("Y-m-d"),
                "location_id" => $request->location_id,
                "weight_id" => $request->weight_id,
                "status" => 'pending',
                "deleted" =>  0,
                "createdate" =>  date("Y-m-d H:i:s"),
                "createuser" =>  $user->userid,
            ]);

            Cylinder::where('cylcode', $cylinder)->update(['requested' => 1]); // Mark the cylinder as requested by someone]);

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $user->userid,
                "module" => "Cylinder",
                "action" => "Assignment",
                "activity" => "Cylinder  {$cylinder} assigned from Mobile with id successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $user->userid,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Assigned successfully",
                "data" => $orderid
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

    public function assignBulkCylinder(Request $request)
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
                    "message" => "Bulk Assignment failed. " . join(". ", $validator->errors()->all()),
                ], 422);
            }

            $token = $this->extractToken($request);

            if ($token) {
                $user = User::where('remember_token', $token)->first();
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized - Token not provided or invalid'
                ], 401);
            }

            DB::beginTransaction();
            $orderid = strtoupper(bin2hex(random_bytes(6)));
            foreach ($request->bulk_items as $item) {
                $cylinders = Cylinder::where('weight_id', $item['weight_id'])->where('requested', 0)->limit($item['qty'])->get('cylcode');

                if (count($cylinders) === 0) {
                    return response()->json(['status' => false, 'message' => 'There are no available cylinders'], 406);
                }

                $cylinderExists = CustomerCylinder::whereIn("cylcode", $cylinders)->exists();
                if ($cylinderExists) {
                    return response()->json(['status' => false, 'message' => 'Cylinder already assigned to a customer'], 406);
                }
                foreach ($cylinders as $cylinder) {
                    DB::table("tblcustomer_cylinder")->insert([
                        "transid" => strtoupper(bin2hex(random_bytes(4))),
                        "order_id" => $orderid,
                        "custno" => $user->userid,
                        "cylcode" => $cylinder->cylcode,
                        "date_acquired" => $request->date ?? date("Y-m-d"),
                        "status" => 'pending',
                        "deleted" =>  0,
                        "createdate" =>  date("Y-m-d H:i:s"),
                        "createuser" =>  $user->userid,
                    ]);
                }
                Cylinder::whereIn('cylcode', $cylinders)->update([
                    'requested' => 1,
                    "location_id" => $request->location_id
                ]); // Mark the cylinder as requested by someone]);
            }


            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $user->userid,
                "module" => "Cylinder",
                "action" => "Assignment",
                "activity" => "Cylinder  {$cylinder} assigned from Mobile with id successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $user->userid,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Assigned successfully",
                "data" => $orderid
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

    public function refillCylinder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                // "refill_items.*.qty" => "required",
                "refill_items.*.cylcode" => "required",
                // "location_id" => "required",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => false,
                    "message" => "Cylinder refill failed. " . join(". ", $validator->errors()->all()),
                ], 422);
            }

            $token = $this->extractToken($request);

            if ($token) {
                $user = User::where('remember_token', $token)->first();
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized - Token not provided or invalid'
                ], 401);
            }

            DB::beginTransaction();
            $orderid = 'RF' . strtoupper(bin2hex(random_bytes(3)));
            foreach ($request->refill_items as $item) {
                $oldCylinder = Cylinder::where('cylcode', $item['cylcode'])->first();
                $newCylinder = Cylinder::where('weight_id', $oldCylinder->weight_id)->where('requested', 0)->first();

                // if (count($cylinders) === 0) {
                //     return response()->json(['status' => false, 'message' => 'There are no cylinders assigned to this user'], 406);
                // }

                CustomerCylinder::insert([
                    "transid" => strtoupper(bin2hex(random_bytes(4))),
                    "order_id" => $orderid,
                    "custno" => $user->userid,
                    "cylcode" => $newCylinder->cylcode,
                    "date_acquired" => $request->date ?? date("Y-m-d"),
                    "status" => 'pending',
                    "deleted" =>  0,
                    "createdate" =>  date("Y-m-d H:i:s"),
                    "createuser" =>  $user->userid,
                ]);
                Cylinder::where('cylcode', $newCylinder->cylcode)->update([
                    'requested' => 1,
                    "location_id" => $request->location_id
                ]); // Mark the cylinder as requested by someone]);

                Cylinder::where('cylcode', $oldCylinder->cylcode)->update([
                    'requested' => 2,
                    "location_id" => null
                ]); // Now set old cylinder as not requested

                Exchange::insert([
                    "transid" => strtoupper(bin2hex(random_bytes(4))),
                    "custno" => $user->userid,
                    "order_id" => $orderid,
                    "cylcode_old" => $oldCylinder->cylcode,
                    "cylcode_new" => $newCylinder->cylcode,
                    "status" => "pending",
                    "deleted" =>  0,
                    "createdate" =>  date("Y-m-d H:i:s"),
                    "createuser" =>  $user->userid,
                ]);
            }

            Cylinder::whereIn('cylcode', $request->refill_items)->update([
                'requested' => 0,
                "location_id" => null
            ]); // Now set old cylinder as not requested
            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $user->userid,
                "module" => "Cylinder",
                "action" => "Assignment",
                "activity" => "Cylinder  {$newCylinder->cylcode} assigned from Mobile with id successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $user->userid,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Assigned successfully",
                "data" => $orderid
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
     * TODO::After a refill request, during exchange a new cylinder code will be assigned to
     * the customer and request will be set to 1 and the old cylinder code will be set to 0
     * 
     */
    public function exchange(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "oldCylinderNo" => "required",
                "newCylinderNo" => "required",
                "vendorNo" => "required",
                "customerNo" => "required",
                "newBarcode" => "required",
                "oldBarcode" => "required",
                "customerPhone" => "required",
                // "amountDue" => "required",
                "amountPaid" => "required",
                // "balance" => "required",

            ], [
                // This has our own custom error messages for each validation
                "oldCylinderNo.required" => "No old cylinder supplied",
                "newCylinderNo.required" => "No new cylinder selected",
                "vendorNo.required" => "No vendor ID supplied",
                "customerNo.required" => "No customer numberr supplied",
                "newBarcode.required" => "No new barcode supplied",
                "oldBarcode.required" => "No old barcode supplied",
                "paymentMode.required" => "Payment Mode is required",
                "customerPhone.required" => "Customer phone number is required",
                // "amountDue.required" => "Amount Due is required",
                "amountPaid.required" => "Amount Paid is required",
                // "balance.required" => "Balance is required",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Cylinder Exchange failed. " . join(". ", $validator->errors()->all()),
                ]);
            }

            DB::beginTransaction();
            $oldCyinder = DB::table("tblcylinder")->select('size')->where('cylcode', $request->oldCylinderNo)->where("deleted", 0)->first();
            $newCyinder = DB::table("tblcylinder")->select('size')->where('cylcode', $request->newCylinderNo)->where("deleted", 0)->first();

            $transid = strtoupper(bin2hex(random_bytes(4)));
            DB::table("tblexchange")->insert([
                "transid" => $transid,
                "custno" => $request->customerNo,
                "vendor_no" => $request->vendorNo,
                "cylcode_old" => $request->oldCylinderNo,
                "cylcode_new" => $request->newCylinderNo,
                "new_cylinder_size" => $newCyinder->size,
                "old_cylinder_size" => $oldCyinder->size,
                "barcode" => $request->newBarcode,
                "litres" => $request->litres,
                "customer_phone" => $request->customerPhone,
                "vendor_pictures" => $request->vendorCylinderPics,
                "customer_pictures" => $request->customerCylinderPics,
                "longitude" => $request->longitude,
                "latitude" => $request->latitude,
                "deleted" =>  0,
                "createdate" =>  date("Y-m-d H:i:s"),
                "createuser" =>  $request->createuser,
            ]);

            $upExchange = DB::table("tblcustomer_cylinder")->where('cylcode', $request->oldCylinderNo)
                ->where('barcode', $request->oldBarcode)->where("deleted", 0)->first();

            // if (empty($upExchange)) {
            //     return response()->json([
            //         "ok" => false,
            //         "msg" => "Record not found",
            //     ]);
            // }

            if (!empty($upExchange)) {
                DB::table("tblcustomer_cylinder")->where('cylcode', $request->oldCylinderNo)
                    ->where('barcode', $request->oldBarcode)
                    ->update([
                        "status" => 0,
                        "modifydate" =>  date("Y-m-d H:i:s"),
                        "modifyuser" =>  $request->createuser,
                    ]);
            }

            DB::table("tblcustomer_cylinder")->insert([
                "transid" => $transid,
                "custno" => $request->customerNo,
                "cylcode" => $request->newCylinderNo,
                "date_acquired" => date("Y-m-d"),
                "vendor_no" => $request->vendorNo,
                "barcode" => $request->newBarcode,
                "status" => 1,
                "deleted" =>  0,
                "createdate" =>  date("Y-m-d H:i:s"),
                "createuser" =>  $request->createuser,
            ]);

            DB::table("tblpayment")->insert([
                "transid" => $transid,
                "custno" => $request->customerNo,
                "payment_mode" => $request->paymentMode,
                "amount_due" => $request->amountDue,
                "amount_paid" => $request->amountPaid,
                "balance" => 0,
                "cylcode" => $request->newCylinderNo,
                "barcode" => $request->newBarcode,
                "deleted" =>  0,
                "createdate" =>  date("Y-m-d H:i:s"),
                "createuser" =>  $request->createuser,
            ]);

            $customer = DB::table("tblcustomer")->where('custno', $request->customerNo)->where("deleted", 0)->first();

            if (!empty($request->customerPhone)) {
                $vendor = DB::table("tblvendor")->where('vendor_no', $request->vendorNo)->where("deleted", 0)->first();

                $date = gmdate("jS M Y, h:iA");
                $msg = <<<MSG
            Thank you for your business and trust. It is our pleasure to work with you. Below is a receipt for the cylinder exchange.

            Customer name: {$customer->fname} {$customer->mname} {$customer->lname}
            Old cylinder number: {$request->oldCylinderNo}
            New cylinder number: {$request->newCylinderNo}
            Payment Mode: {$request->paymentMode}
            Amount Paid: {$request->amountPaid}
            Vendor Name: {$vendor->fname} {$vendor->mname} {$vendor->lname}
            Date: {$date}
            MSG;

                $sms = new ASms('PETROCELL', env('ARKESEL_SMS_API_KEY'));
                $sms->send($request->customerPhone, $msg);
            }

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "Cylinder",
                "action" => "Register",
                "activity" => "Cylinder registered from Mobile with id {$request->cylcode} successfully",
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
                "errMsg" => $e->getMessage(),
            ]);
        }
    }

    public function assignCondition(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "condition" => "required",
                "barcode" => "required",
            ], [
                // This has our own custom error messages for each validation
                "barcode.required" => "No barcode supplied",
                "condition.required" => "No condition selected",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Cylinder Condition Update failed. " . join(". ", $validator->errors()->all()),
                ]);
            }

            DB::beginTransaction();
            $exchange =  DB::table("tblexchange")->where("barcode", $request->barcode)->where("deleted", 0)->first();

            if (empty($exchange)) {
                return response()->json([
                    "ok" => false,
                    "msg" => "No exchange record found for this cylinder",
                ]);
            }

            DB::table("tblexchange")->where("barcode", $request->barcode)->where("deleted", 0)->update([
                "cylcode_condition" => $request->condition,
                "longitude" => $request->longitude,
                "latitude" => $request->latitude,
                "modifydate" =>  date("Y-m-d H:i:s"),
                "modifyuser" =>  $request->modifyuser,
            ]);

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid,
                "username" => $request->modifyuser,
                "module" => "Cylinder",
                "action" => "Condition",
                "activity" => "Cylinder with barcode {$request->barcode}'s condition updated from Mobile successfully",
                "ipaddress" => $userIp,
                "createuser" =>  $request->modifyuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);

            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "Cylinder condition updated successfully",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured during cylinder conditon update", [
                "errMsg" => $e->getMessage(),
                "trace" => $e->getTrace(),
            ]);

            return response()->json([
                "ok" => false,
                "errMsg" => $e->getMessage(),
                "msg" => "Request failed. An internal error occured",
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
