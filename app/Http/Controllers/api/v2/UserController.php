<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\Models\CustomerLocation;
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
        return $this->userService->changePassword($this->request->all(), $user);
    }

    public function createOrder()
    {
        $user = $this->request->user();
        return $this->userService->createOrder($this->request->all(), $user);
    }

    public function addLocation()
    {
        $user = $this->request->user();
        return $this->userService->createLocation($this->request->all(), $user);
    }

    public function setDefaultLocation($id)
    {
        $user = $this->request->user();
        return $this->userService->setDefaultLocation($this->request->all(), $id, $user);
    }

    public function getLocation()
    {
        $user = $this->request->user();
        $location = CustomerLocation::where('customer_id', $user->customer_id)->get();
        return apiSuccessResponse('Request Successful', 200, $location);
    }
}
