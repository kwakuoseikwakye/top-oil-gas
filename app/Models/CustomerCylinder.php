<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCylinder extends Model
{
    use HasFactory;
    const CREATED_AT = "createdate";
    const UPDATED_AT = "modifydate";

    const DELIVERY = 'delivery';
    const PICKUP = 'pickup';
    const PENDING = 'pending';
    const SUCCESS = 'success';
    const CANCELLED = 'cancelled';
    const EN_ROUTE = 'en route';
    const DELIVERED = 'delivered';
    const ORDER_TYPE_PICKUP_NOW = 'pickup_now';
    const ORDER_TYPE_PICKUP_LATER = 'pickup_later';

    protected $table = "tblcustomer_cylinder";
    protected $primaryKey = "transid";
    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = [
        "transid", "custno", "cylcode", "barcode", "date_acquired",
        "vendor_no", "status", "deleted", "createdate","location_id",
        "createuser", "modifydate", "modifyuser","weight_id","order_id"
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, "custno", "custno");
    }

    public function cylinder()
    {
        return $this->belongsTo(Cylinder::class, "cylcode", "cylcode");
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, "vendor_no", "vendor_no");
    }

    public function user()
    {
        return $this->belongsTo(User::class, "custno", "userid");
    }

    public function cylinderWeights() 
    {
        return $this->belongsTo(CylinderSize::class, "weight_id", "id");
    }
}
