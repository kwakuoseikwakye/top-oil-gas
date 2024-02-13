<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    const CREATED_AT = "createdate";
    const UPDATED_AT = "modifydate";

    protected $table = "tblwarehouse";
    protected $primaryKey = "transid";
    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = [
        "transid", "wcode", "wname", "region",
        "town", "streetname", "landmark",
         "gpsaddress", "phone", "email", "deleted", "createdate",
        "createuser", "modifydate", "modifyuser",
    ];
}
