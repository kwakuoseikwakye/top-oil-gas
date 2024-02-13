<?php

namespace App\Http\Middleware;

use App\Http\Controllers\OTPController;
use App\Http\Controllers\RouteController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $modName = FacadesRequest::route()->getName();
        $currMod = DB::table("tblmodule")
            ->where('modURL',  $modName)
            ->where('modStatus', '1')
            ->first();

        $permissions = DB::table("tblmodule_priv")
            ->where('userid', Auth::user()->email)
            ->where('modID', $currMod->modID)
            ->first();

        // $permissions = RouteController::permissions($modName);
        $otpIsVerified = OTPController::OTPIsVerified($request, $permissions->modID);
        
        if (!$otpIsVerified) {
            OTPController::sendOTP(
                OTPController::generateAndStoreOTP($request, $permissions->modID), 
                $modName
            );
    
            return redirect("/confirm_otp")->with("modID", $permissions->modID)->with("modName", $modName);
        }

        return $next($request);
    }
}
