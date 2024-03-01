<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerLocation extends Model
{
    use HasFactory;

    protected $table = "tblcustomer_location";
    protected $primaryKey = "id";
    public $incrementing = false;

    protected $fillable = [
        "name", "custno", "phone1", "phone2", "address_info",
        "long", "lat", "default", "cylcode",
    ];

    public function cylinders()
    {
        return $this->hasMany(Cylinder::class, 'location_id');
    }
}
