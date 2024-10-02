<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\Models\CylinderWeights;
use Illuminate\Http\Request;

class CylinderController extends Controller
{
    public function getWeight()
    {
        return apiSuccessResponse('Request successful', 200, CylinderWeights::all());
    }
}
