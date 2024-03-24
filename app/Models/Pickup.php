<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pickup extends Model
{
    use HasFactory;

    const CREATED_AT = "createdate";
    const UPDATED_AT = "modifydate";

    protected $table = "tblpickup";
    protected $primaryKey = "id";
    public $incrementing = false;

    protected $fillable = [
        "name", "address", "contact_info", "opening_hours",
    ];

    public function cylinder()
    {
        return $this->belongsTo(Cylinder::class, "cylcode", "cylcode");
    }
}
