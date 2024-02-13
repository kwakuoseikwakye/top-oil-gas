<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CustomerRegistrationController extends Controller
{
    public function customerRegistration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "phone" => "required",
            "idtype" => "required",
            "idNumber" => "required",
            "gender" => "required",
            "address" => "required",
        ], [
            // This has our own custom error messages for each validation
            "name.required" => "No customer name supplied",
            "phone.required" => "No phone supplied",
            "idtype.required" => "No id type supplied",
            "idNumber.required" => "No id number supplied",
            "gender.required" => "No gender supplied",
            "address.required" => "No address supplied",


        ]);

        if ($validator->fails()) {
            return response()->json([
                "ok" => false,
                "msg" => "Adding customer failed. " . join(". ", $validator->errors()->all()),
            ]);
        }

        try {

            $transactionResult = DB::transaction(function () use ($request) {

                DB::table("customer")->insert([
                    'id' => "1",
                    'regtype' =>  $request->regType,
                    'customername' => $request->name,
                    'telnumber' => $request->phone,
                    'idtype' => $request->idtype,
                    'idnumber' => $request->idNumber,
                    'gender' => $request->gender,
                    'dob' => $request->dob,
                    'vehicles' => $request->vehicles,
                    'address' => $request->address,
                    'nameinistials' => $request->nameInitials,
                    'applicationnumber' => $request->applicationNumber,
                    'cylindernumber' => $request->cylinderNumber,
                    'cylindernumbers' => $request->cylinderNumber,
                    'ghanapostgprs' => $request->gps,
                    'account' => $request->cylinderNumber,
                    'longitude' => $request->longitude,
                    'latitude' => $request->latitude,
                    'initialown' => "0",
                    'barcode' => $request->barcode,
                    'serialno' => $request->serialNo,
                    'cylindertype' => "2",
                    'amount' => $request->amount,
                    'active' => '1',
                    'dateregistered' => date('Y-m-d H:i:s'),
                    'dateupdate' => date('Y-m-d H:i:s')
                ]);
            });


            if (!empty($transactionResult)) {
                throw new Exception($transactionResult);
            }
            return response()->json([
                "ok" => true,
                "msg" => "Customer added successfully"
            ]);
        } catch (\Exception $e) {
            Log::error("Failed adding customer: " . $e->getMessage());
            return response()->json([
                "ok" => false,
                "msg" => "Adding customer failed",
                "error" => [
                    "msg" => $e->__toString(),
                    "err_msg" => $e->getMessage(),
                    "fix" => "Please complete all required fields",
                ]
            ]);
        }
    }

    public function addCylinder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "owner" => "required",
            "cylinderNumber" => "required",
            "barcode" => "required",
            "capacity" => "required",
        ], [
            // This has our own custom error messages for each validation
            "owner.required" => "No owner supplied",
            "cylinderNumber.required" => "No cylinder number supplied",
            "barcode.required" => "No barcode supplied",
            "capacity.required" => "No capacity supplied",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "ok" => false,
                "msg" => "Adding cylinder failed. " . join(". ", $validator->errors()->all()),
            ]);
        }

        try {

            $transactionResult = DB::transaction(function () use ($request) {

                DB::table("cylinder")->insert([
                    'owner' =>  $request->owner,
                    'ownerinformation' => $request->ownerInformation,
                    'ownerid' => strtoupper(strtoupper(bin2hex(random_bytes(5)))),
                    'cylindernumber' => $request->cylinderNumber,
                    'Barcode' => $request->barcode,
                    'clinderserial' => $request->cylinderSerial,
                    'capacity' => $request->capacity,
                    'oldcylindernumber' => $request->oldCylinderNumber,
                    'active' => '1',
                ]);
            });


            if (!empty($transactionResult)) {
                throw new Exception($transactionResult);
            }
            return response()->json([
                "ok" => true,
                "msg" => "Cylinder added successfully"
            ]);
        } catch (\Exception $e) {
            Log::error("Failed adding cylinder: " . $e->getMessage());
            return response()->json([
                "ok" => false,
                "msg" => "Adding cylinder failed",
                "error" => [
                    "msg" => $e->__toString(),
                    "err_msg" => $e->getMessage(),
                    "fix" => "Please complete all required fields",
                ]
            ]);
        }
    }

    public function fetchCylinders()
    {
        $cylinders = DB::table("cylinder")->where("active","1")->get();

        return response()->json([
            "data" => $cylinders
        ]);
    }

    public function fetchCustomers()
    {
        $cus = DB::table("customer")->where("active","1")->get();

        return response()->json([
            "data" => $cus
        ]);
    }

    public function fetchCylinderCapacity()
    {
        $cus = DB::table("volume")->where("active","1")->get();

        return response()->json([
            "data" => $cus
        ]);
    }
}
