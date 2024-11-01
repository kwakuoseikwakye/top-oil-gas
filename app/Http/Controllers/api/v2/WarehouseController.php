<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\Models\Pickup;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function getPickupLocations()
    {
        $pickups = Pickup::all();
        return apiSuccessResponse('Request successful', 200, $pickups);
    }
}
