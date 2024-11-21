<?php

namespace App\Http\Controllers;

use App\Models\CustomerCylinder;
use App\Models\Cylinder;
use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CylinderController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function assignCylinder()
    {
        $validator = Validator::make(
            $this->request->all(),
            [
                "cylinder_code" => "required|array",
                "cylinder_code.*" => "required|string",  // Expecting each cylinder code to be a string
                "custno" => "required",
                "order_id" => "required|exists:orders,id",
            ]
        );
    
        if ($validator->fails()) {
            return apiErrorResponse("Assign failed. " . join(" ", $validator->errors()->all()), 422);
        }
    
        try {
            $order = Orders::find($this->request->order_id);
            $cylinderCount = count($this->request->cylinder_code);
    
            if ($order->quantity !== $cylinderCount) {
                return apiErrorResponse('Please select the required quantity for the order', 422);
            }
    
            DB::beginTransaction();
    
            foreach ($this->request->cylinder_code as $code) {
                CustomerCylinder::create([
                    'order_number' => $order->order_number,
                    'cylcode' => $code  // Use each code directly as a string
                ]);
    
                Cylinder::where('code', $code)->update([
                    'requested' => true,
                ]);
            }
    
            DB::commit();
            return apiSuccessResponse("Cylinder assigned successfully");
    
        } catch (\Exception $e) {
            DB::rollBack();
            return apiErrorResponse("An internal error occurred", 500, $e);
        }
    }
    
}
