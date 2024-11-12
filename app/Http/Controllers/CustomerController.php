<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Imports\CustomerImport;
use App\Models\Customer;
use App\Models\Log as ModelsLog;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Stevebauman\Location\Facades\Location;
use App\Arkesel\Arkesel as Sms;
use App\Services\OtpService;
use App\Services\SmsService;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'data' => CustomerResource::collection(Customer::all()),
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
    public function store(Request $request, SmsService $sms)
    {
        try {
            $validator = Validator::make($request->all(), [
                "fname" => "required",
                "lname" => "required",
                "phone" => "required|unique:users,phone",
                "id_type" => "required",
                "id_no" => "required",
            ], [
                "fname.required" => "No first name supplied",
                "lname.required" => "No last name supplied",
                "phone.required" => "No phone number supplied",
                "phone.unique" => "Phone number already taken",
                "id_type.required" => "ID Type is required",
                "id_no.required" => "ID number is required",
            ]);

            if ($validator->fails()) {
                return apiErrorResponse("Registration failed. " . join(". ", $validator->errors()->all()));
            }

            DB::beginTransaction();

            $pass = uniqid();

            $customer = Customer::create([
                "fname" => strtoupper($request->fname),
                "lname" => strtoupper($request->lname),
                "id_type" => $request->id_type,
                "id_no" => $request->id_no,
                "agent_code" => $request->agent_code,
            ]);
            User::create([
                'customer_id' => $customer->id,
                "username" => "{$request->fname} {$request->lname}",
                "usertype" => "customer",
                "password" =>  Hash::make($pass),
                "phone" => empty($request->phone) ? '' : $request->phone,
            ]);

            if (null != $request->hasFile('idimage')) {

                $filePath = $request->file("idimage")->store("public/ids");

                Customer::where("id", $customer->id)->update([
                    "id_link" => env("IMAGE_BASE_URL") . "/" . str_replace("public", "storage", $filePath),
                ]);
            }


            if (null != $request->hasFile('image')) {

                $filePath = $request->file("image")->store("public/avatars");

                Customer::where("id", $customer->id)->update([
                    "picture" => env("IMAGE_BASE_URL") . "/" . str_replace("public", "storage", $filePath),
                ]);
            }

            DB::commit();
            $msg = <<<MSG
            Hi {$request->fname},
            Thanks for registering with TOPOIL.
            Kindly use the following credentials to login
            into our mobile app. Your password is {$pass}
            MSG;

            $sms->sendMessage($request->phone, $msg);

            return apiSuccessResponse('Customer added successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return apiErrorResponse('An error occurred adding customer', 500, $e);
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
        return view('components.customercomponent', [
            'title' => 'Customer Component Title',
            'description' => 'This is a description for the customer component.'
        ]);
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
   {}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
   {}
}
