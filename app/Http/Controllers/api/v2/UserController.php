<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;
    protected $request;

    public function __construct(UserService $userService, Request $request)
    {
        $this->userService = $userService;
        $this->request = $request;
    }

    public function changePassword()
    {
        $user = $this->request->user();
        if (!$user) {
            return apiErrorResponse("Unauthorised user", 400);
        }
        return $this->userService->changePassword($this->request->all(), $user);
    }
}
