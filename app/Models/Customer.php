<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    const CREATED_AT = "createdate";
    const UPDATED_AT = "modifydate";

    protected $table = "tblcustomer";
    protected $primaryKey = "transid";
    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = [
        "transid", "custno", "title", "fname",
        "mname", "lname", "gender", "dob",
        "pob", "marital_status", "occupation",
        "home_address", "landmark", "town",
        "phone", "gpsaddress", "streetname",
        "region", "id_type", "id_no", "id_link",
         "longitude", "latitude", "deleted", "createdate",
        "createuser", "modifydate", "modifyuser",
    ];

    public function cylinders()
    {
        return $this->hasMany(CustomerCylinder::class, "custno", "custno");
    }

    public function user()
    {
        return $this->belongsTo(User::class, "custno", "userid");
    }
}
