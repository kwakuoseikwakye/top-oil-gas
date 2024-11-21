<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Orders;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $data  = Orders::select(
            'orders.*',
            'cylinder_weights.weight',
            'cylinder_weights.amount',
            'customer_locations.name',
            'customer_locations.phone1',
            'customer_locations.phone2',
            'customer_locations.address',
            'customer_locations.additional_info',
            'customers.fname',
            'customers.lname',
            'cylinders.requested',
            'cylinders.code'
        )
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->leftJoin('customer_cylinders', 'customer_cylinders.order_number', 'orders.order_number')
            ->leftJoin('cylinders', 'cylinders.code', 'customer_cylinders.cylcode')
            ->leftJoin('customer_locations', 'customer_locations.id', 'orders.location_id')
            ->leftJoin('pickups', 'pickups.id', 'orders.pickup_location_id')
            ->leftJoin('cylinder_weights', 'cylinder_weights.id', 'orders.weight_id')
            ->orderByDesc('orders.date_acquired')->get();
        return response()->json([
            'data' => OrderResource::collection($data)
        ]);
    }
}
