<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class OtpService
{
    public function generateOtp()
    {
        return rand(100000, 999999);
    }

    public function storeOtp($phone, $otp, $duration = 600)
    {
        Cache::put('otp_' . $phone, $otp, $duration);
    }

    public function getOtp($phone)
    {
        return Cache::get('otp_' . $phone);
    }

    public function forgetOtp($phone)
    {
        Cache::forget('otp_' . $phone);
    }
}
