<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected $authService;
    protected $request;

    public function __construct(AuthService $authService, Request $request)
    {
        $this->authService = $authService;
        $this->request = $request;
    }

    public function signUp()
    {
        return $this->authService->signUp($this->request->all());
    }

    public function verifyOtp()
    {
        return $this->authService->verifyOtp($this->request->all());
    }

    public function login()
    {
        return $this->authService->login($this->request->only(['phone', 'password']));
    }

    public function sendOtp()
    {
        return $this->authService->sendOtp($this->request->all());
    }

    public function passwordReset()
    {
        return $this->authService->passwordReset($this->request->all());
        
    }
}
