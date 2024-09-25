<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Enums\Status;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class AuthService
{
      const ALLOWED_USER_TYPES = ['customer'];
      protected $smsService;
      protected $otpService;

      public function __construct(SmsService $smsService, OtpService $otpService)
      {
            $this->smsService = $smsService;
            $this->otpService = $otpService;
      }

      /**
       * Generate the throttle key using phone number and request IP.
       */
      private function getThrottleKey(string $phone): string
      {
            return 'login-attempts:' . $phone . '|' . request()->ip();
      }

      public function signUp(array $data)
      {
            $validator = Validator::make($data, [
                  "fname" => "required",
                  "lname" => "required",
                  "phone" => "required|unique:users,phone",
                  "password" => "required|min:8",
                  "id_type" => "nullable",
                  "id_no" => "nullable",
                  "id_link" => "nullable",
            ], [
                  "fname.required" => "No first name supplied",
                  "lname.required" => "No last name supplied",
                  "phone.required" => "No phone number supplied",
                  "phone.numeric" => "Phone number supplied must contain only numbers",
                  "phone.unique" => "Phone number already taken",
                  "password.required" => "No password supplied",
                  "password.min" => "Your password must be a minimum of 8 characters long",
            ]);

            if ($validator->fails()) {
                  return apiErrorResponse("Registration failed. " . join(". ", $validator->errors()->all()), 422);
            }

            try {
                  DB::beginTransaction();

                  $filePath = $data['id_link']->store('images', 'public');
                  $customer = Customer::create([
                        "fname" => $data['fname'],
                        "lname" => $data['lname'],
                        "id_type" => $data['id_type'] ?? null,
                        "id_no" => $data['id_no'] ?? null,
                        "id_link" => $data['id_link'] ? $filePath : null,
                  ]);

                  User::create([
                        "customer_id" => $customer->id,
                        "username" => $data['fname'] . ' ' . $data['lname'],
                        "usertype" => Status::CUSTOMER,
                        "password" => Hash::make($data['password']),
                        "phone" => $data['phone'],
                  ]);

                  DB::commit();

                  $this->smsService->sendOtp($data['phone']);

                  return apiSuccessResponse('Signup successful');
            } catch (\Throwable $e) {
                  DB::rollBack();
                  return apiErrorResponse('Internal error occurred', 500, $e);
            }
      }

      public function verifyOtp(array $data)
      {
            $validator = Validator::make($data, [
                  "phone" => "required|exists:users,phone",
                  "otp" => "required|integer|min:6",
            ]);

            if ($validator->fails()) {
                  return apiErrorResponse("Otp verification failed. " . join(". ", $validator->errors()->all()), 422);
            }

            $phone = $data['phone'];
            $otp = $data['otp'];

            $cachedOtp = $this->otpService->getOtp($phone);

            if ($cachedOtp && $cachedOtp == $otp) {
                  User::where('phone', $phone)->update([
                        'verified' => true,
                        'email_verified_at' => now()
                  ]);
                  $this->otpService->forgetOtp($phone);
                  return apiSuccessResponse('OTP verification successful');
            }

            return apiErrorResponse('Invalid OTP', 400);
      }

      public function login(array $data)
      {
            $validator = Validator::make($data, [
                  "phone" => "required|exists:users,phone",
                  "password" => "required|min:8",
            ]);

            if ($validator->fails()) {
                  return apiErrorResponse("Login failed. " . join(". ", $validator->errors()->all()), 422);
            }

            $throttleKey = $this->getThrottleKey($data['phone']);
            $maxAttempts = 5;
            $decayMinutes = 1;

            if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
                  $seconds = RateLimiter::availableIn($throttleKey);
                  return apiErrorResponse("Too many login attempts. Try again in {$seconds} seconds.", 429);
            }


            if (!Auth::attempt($data)) {
                  RateLimiter::hit($throttleKey, $decayMinutes);
                  return apiErrorResponse('Invalid credentials', 401);
            }

            RateLimiter::clear($throttleKey);
            $user = User::where("phone", $data['phone'])->first();

            if (!in_array(strtolower($user->usertype), self::ALLOWED_USER_TYPES)) {
                  return apiErrorResponse('Unauthorized access');
            }

            if ($user->verified === false) {
                  return apiErrorResponse('User not verified');
            }


            $token = $user->createToken('accessToken')->plainTextToken;
            User::where("phone", $data['phone'])->update([
                  'remember_token' => $token,
                  'last_login' => date('Y-m-d h:i:s')
            ]);
            $userData = [
                  'access_token' => $token,
                  'user' => $user,
            ];
            return apiSuccessResponse('Login successful', 200, $userData);
      }

      public function sendOtp(array $data)
      {
            $validator = Validator::make($data, [
                  "phone" => "required|exists:users,phone",
            ]);

            if ($validator->fails()) {
                  return apiErrorResponse("Sending otp failed. " . join(". ", $validator->errors()->all()), 422);
            }

            $phone = $data['phone'];

            $this->otpService->forgetOtp($phone);

            $this->smsService->sendOtp($phone);

            return apiSuccessResponse('OTP sent successfully');
      }

      public function passwordReset(array $request)
      {
            $validator = Validator::make(
                  $request,
                  [
                        "password" => "required|min:8",
                        "otp" => "required|min:6",
                        "phone" => "required|numeric|exists:users,phone",
                  ],
                  [
                        "password.required" => "You have to supply your new password",
                        "password.min" => "Your new password must be at least 8 characters long",
                        "otp.min" => "otp must be at least 6 characters long",
                        "phone.required" => "No phone number supplied",
                        "phone.exists" => "Unknown phone number supplied",
                        "phone.numeric" => "The phone number you supplied is invalid",
                  ]
            );


            if ($validator->fails()) {
                  return apiErrorResponse("Reset failed. " . join(" ", $validator->errors()->all()), 422);
            }

            $phone = $request['phone'];
            $authenticatedUser = User::where("phone", $phone)
                  ->first();

            $data = [
                  "phone" => $phone,
                  "otp" => $request['otp']
            ];

            $this->verifyOtp($data);

            $password = Hash::make($request['password']);

            try {
                  $authenticatedUser->update([
                        'password' => $password,
                  ]);

                  $msg = <<<MSG
                        Your password has been reset. If you did not initiate this action, please contact support.
                        MSG;
                  $this->smsService->sendMessage($phone, $msg);

                  return apiSuccessResponse("Password reset successfully");
            } catch (\Exception $th) {
                  return apiErrorResponse("Internal error occurred", 500, $th);
            }
      }
}
