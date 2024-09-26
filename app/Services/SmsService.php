<?php

namespace App\Services;

use App\Arkesel\Arkesel as Sms;
use Illuminate\Support\Facades\Log;

class SmsService
{
      protected $sms;
      protected $otpService;

      public function __construct(OtpService $otpService)
      {
            $this->sms = new Sms('TOP-OIL', env('ARKESEL_SMS_API_KEY'));
            $this->otpService = $otpService;
      }

      public function sendOtp(string $phone)
      {
            $otpCode = $this->otpService->generateOtp();
            Log::info('otp code for' . $phone . ' => '.$otpCode);
            $this->otpService->storeOtp($phone, $otpCode);
            $message = "Your verification code is " . $otpCode;
            return $this->sms->send($phone, $message);
      }

      public function sendMessage(string $phone, string $message)
      {
            return $this->sms->send($phone, $message);
      }
}
