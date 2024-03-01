<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cylinder extends Model
{
    use HasFactory;
    const CREATED_AT = "createdate";
    const UPDATED_AT = "modifydate";

    protected $table = "tblcylinder";
    protected $primaryKey = "transid";
    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = [
        "transid", "barcode", "owner", "cylcode", "size","notes",
        "weight_id", "initial_amount", "images", "deleted", "createdate",
        "createuser", "modifydate", "modifyuser","location_id"
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function customers()
    {
        return $this->belongsTo(CustomerCylinder::class, "cylcode", "cylcode");
    }

    public function user()
    {
        return $this->belongsTo(User::class, "createuser", "createuser");
    }

    public function location()
    {
        return $this->belongsTo(CustomerLocation::class, 'location_id');
    }

    public function cylinderWeight()
    {
        return $this->belongsTo(CylinderSize::class, 'weight_id','id');
    }
}
