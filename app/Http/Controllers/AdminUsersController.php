<?php

namespace App\Http\Controllers;

use App\Http\Resources\PrivilegeResource;
use App\Models\Log as ModelsLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Location\Facades\Location;

class AdminUsersController extends Controller
{
    public function fetchPrivilege($userid)
    {
      $priv =  DB::table("tblmodule")
            ->select('tblmodule_priv.modRead','tblmodule_priv.userid','tblmodule.hasChild','tblmodule.isChild',
            'tblmodule.modName', 'tblmodule.modLabel',
            'tblmodule.modURL', 'tblmodule.modIcon', 'tblmodule.modID')
            ->join('tblmodule_priv','tblmodule.modID', 'tblmodule_priv.modID')
            ->where('tblmodule_priv.userid', $userid)
            // ->where('tblmodule_priv.modRead','1')
            ->where('tblmodule.modStatus','1')
            ->where('tblmodule.isChild','0')
            ->orderBy('tblmodule.arrange', 'ASC')
            ->orderBy('tblmodule.id', 'ASC')
            ->get();

          return  response()->json([
                "data" => PrivilegeResource::collection($priv)
            ]);
    }

    public function updatePriv(Request $request)
    {
        // echo $data;
        $priv = DB::table("tblmodule_priv")->where('userid', $request->userID)
            ->where('modID', $request->modID)->update([
                $request->privType => $request->status
            ]);

        return $priv;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::where("deleted", "0")
        ->where("usertype","!=","customer")
        ->orderByDesc("createdate")->get();

        return response()->json([
            "data" => $users
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
                "fname" => "required",
                "lname" => "required",
                "usertype" => "required",
                "password" => "required",
                "phone" => "required|numeric|unique:tblvendor,phone",
                "email" => "required|email|unique:tbluser,email",
            ], [

                "fname.required" => "No first name supplied",
                "lname.required" => "No last name supplied",
                "password.required" => "No password supplied",

                // Phone error messages
                "phone.required" => "No phone number supplied",
                "phone.numeric" => "Phone number supplied [{$request->phone}] must contain only numbers",
                "phone.unique" => "Phone number already taken",


                // Email error messages
                "email.email" => "The supplied email [{$request->email}] is not a valid email",
                "email.required" => "No email supplied",
                "email.unique" => "Email already taken",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Registration failed." . join(". ", $validator->errors()->all()),
                ]);
            }

            $checkAdminEmailExist = DB::table("tbluser")->where("email",$request->email)->first();

            if (!empty($checkAdminEmailExist)) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Email already taken"
                ]);
            }

            DB::beginTransaction();
            $transid = strtoupper(bin2hex(random_bytes(4)));
            DB::table("tbluser")->insert([
                "transid" => $transid,
                "userid" => 'AD-' . $transid,
                "fname" => $request->fname,
                "lname" => $request->lname,
                "username" => "{$request->fname} {$request->lname}",
                "usertype" => $request->usertype,
                "password" =>  Hash::make($request->password),
                "phone" => empty($request->phone) ? '' : $request->phone,
                "email" => empty($request->email) ? '' : $request->email,
                "picture" => $request->picture,
                "deleted" =>  0,
                "createdate" =>  date("Y-m-d H:i:s"),
                "createuser" =>  $request->createuser,
            ]);

            if (null !== $request->file("image")) {
                $path = $request->file("image")->store("public/user");

                User::where("transid", $transid)->update([
                    "picture" => env("APP_URL") . "/storage/user/" . explode('/', $path)[2],
                ]);
            }

            $mods = DB::table("tblmodule")->get();

            foreach ($mods as $mod) {
                DB::table("tblmodule_priv")->insert([
                    "userid" => $request->email,
                    "modRead" => "1",
                    "modID" => $mod->modID,
                    "createdate" => date("Y-m-d"),
                    "createuser" => "admin",
                ]);
            }

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "User",
                "action" => "Add",
                "activity" => "User added from Back Office with id AD-{$transid} successfully",
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
                "fname" => "required",
                "lname" => "required",
                "usertype" => "required",
                "phone" => "required|numeric",
                "email" => "required|email|unique:tbluser,email",
            ], [

                "fname.required" => "No first name supplied",
                "lname.required" => "No last name supplied",

                // Phone error messages
                "phone.required" => "No phone number supplied",
                "phone.numeric" => "Phone number supplied [{$request->phone}] must contain only numbers",
            
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Registration failed." . join(". ", $validator->errors()->all()),
                ]);
            }

            DB::beginTransaction();
            $transid = strtoupper(bin2hex(random_bytes(4)));
            DB::table("tbluser")->where("transid",$request->transid)->update([
                "fname" => $request->fname,
                "lname" => $request->lname,
                "username" => "{$request->fname} {$request->lname}",
                "usertype" => $request->usertype,
                "password" =>  Hash::make($request->password),
                "phone" => empty($request->phone) ? '' : $request->phone,
                "email" => empty($request->email) ? '' : $request->email,
                "picture" => $request->picture,
                "deleted" =>  0,
                "modifydate" =>  date("Y-m-d H:i:s"),
                "modifyuser" =>  $request->createuser,
            ]);

            if (null !== $request->file("image")) {
                $path = $request->file("image")->store("public/user");

                User::where("transid", $transid)->update([
                    "picture" => env("APP_URL") . "/storage/user/" . explode('/', $path)[2],
                ]);
            }

            $userIp = $request->ip();
            $locationData = Location::get($userIp);
            $transid1 = strtoupper(bin2hex(random_bytes(4)));

            ModelsLog::insert([
                "transid" => $transid1,
                "username" => $request->createuser,
                "module" => "User",
                "action" => "Update",
                "activity" => "User details updated from Back Office with id AD-{$transid} successfully",
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
            Log::error("An error occured during update", [
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
    public function destroy(Request $request)
    {

        try {
            DB::beginTransaction();
            $user = User::where("transid", $request->transid)->where("deleted", 0)->first();

            if (empty($user)) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Unknown code supplied",
                ]);
            }

           $updated = $user->update([
                "deleted" => 1,
            ]);

            DB::table("tblmodule_priv")->where("userid",$user->email)->delete();

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
                "module" => "User",
                "action" => "Delete user Assigned",
                "activity" => "Deleted user assigned with id {$user->transid}",
                "ipaddress" => $userIp,
                "createuser" => $request->createuser,
                "createdate" => gmdate("Y-m-d H:i:s"),
                "longitude" => $locationData->longitude ?? $userIp,
                "latitude" => $locationData->latitude ?? $userIp,
            ]);
            DB::commit();

            return response()->json([
                "ok" => true,
                "msg" => "user Deleted successfully",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("An error occured deleting user", [
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
