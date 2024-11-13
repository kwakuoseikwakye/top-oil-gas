<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cylinder extends Model
{
    use HasFactory;
    const CREATED_AT = "createdate";
    const UPDATED_AT = "modifydate";

    protected $table = "cylinders";
    protected $primaryKey = "transid";
    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = [
        "id",
        "owner",
        "code",
        "size",
        "weight_id",
        "requested",
        "image",
        "location_id"
    ];

    protected $casts = [
        'image' => 'array',
        'requested' => 'boolean',
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
        return $this->belongsTo(CylinderSize::class, 'weight_id', 'id');
    }
}
