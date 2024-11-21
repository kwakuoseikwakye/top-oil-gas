<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cylinder extends Model
{
    use HasFactory;

    protected $table = "cylinders";
    protected $primaryKey = "id";
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
        return $this->belongsTo(CustomerCylinder::class, "cylcode", "code");
    }

    public function cylinderWeight()
    {
        return $this->belongsTo(CylinderSize::class, 'weight_id', 'id');
    }
}
