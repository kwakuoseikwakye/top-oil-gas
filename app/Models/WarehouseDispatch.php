<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseDispatch extends Model
{
    use HasFactory;
    const CREATED_AT = "createdate";
    const UPDATED_AT = "modifydate";

    protected $table = "tblwarehouse_dispatch";
    protected $primaryKey = "transid";
    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = [
        "transid", "cylcode", "vendor_no", "cylinder_size",
        "from_warehouse", "to_warehouse","deleted", "createdate",
        "createuser", "modifydate", "modifyuser",
    ];

    public function cylinder()
    {
        return $this->belongsTo(Cylinder::class, "cylcode", "cylcode");
    }

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, "from_warehouse", "wcode");
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, "to_warehouse", "wcode");
    }

    public function warehouseUser()
    {
        return $this->belongsTo(User::class, "vendor_no", "userid");
    }
}
