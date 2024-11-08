<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class OtpService
{
    public function generateOtp()
    {
        return rand(100000, 999999);
    }

    public function storeOtp($phone, $otp, $duration = 5)
    {
        User::where('phone', $phone)->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes($duration)
        ]);
    }

    public function getOtp($phone)
    {
        $otp = User::where('phone', $phone)->first()->otp;
        return $otp;
    }

    public function forgetOtp($phone)
    {
        User::where('phone', $phone)->update([
            'otp' => null,
            'otp_expires_at' => null
        ]);
    }
}
