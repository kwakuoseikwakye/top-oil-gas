<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    use HasFactory;
    const CREATED_AT = "createdate";
    const UPDATED_AT = "modifydate";

    const CANCELLED = 'cancelled';
    const PENDING = 'pending';
    const EN_ROUTE = 'en route';
    const DELIVERED = 'delivered';

    protected $table = "tbldispatch";
    protected $primaryKey = "transid";
    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = [
        "transid", "cylcode", "vendor_no", "order_id",
        "dispatch", "deleted", "createdate","pickup_location",
        "createuser", "modifydate", "modifyuser",
    ];

    public function cylinder()
    {
        return $this->belongsTo(Cylinder::class, "cylcode", "cylcode");
    }

    public function order()
    {
        return $this->belongsTo(CustomerCylinder::class, "order_id", "order_id");
    }

    public function pickup()
    {
        return $this->belongsTo(Pickup::class, "pickup_location", "id");
    }

    public function customerLocation()
    {
        return $this->belongsTo(CustomerLocation::class, "location_id", "id");
    }
}
