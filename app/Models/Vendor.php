<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    const CREATED_AT = "createdate";
    const UPDATED_AT = "modifydate";

    protected $table = "tblvendor";
    protected $primaryKey = "transid";
    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = [
        "transid", "vendor_no", "fname", "lname",
        "mname", "phone", "email", "id_type", "id_no",
        "id_file_link", "region", "town", "picture",
        "username", "streetname", "landmark", "gpsaddress",
        "gender", "dob", "approved", "deleted", "createdate",
        "createuser", "modifydate", "modifyuser",
    ];

    public function cylinders_not_returned()
    {
        return $this->hasMany(Dispatch::class, "vendor_no", "vendor_no")->where("dispatch", 0)->where("deleted", 0)->orderBy('createdate', 'DESC');
    }
}
