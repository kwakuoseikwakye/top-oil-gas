<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    use HasFactory;
    const CREATED_AT = "createdate";
    const UPDATED_AT = "modifydate";

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
}
