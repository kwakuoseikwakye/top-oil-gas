<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    use HasFactory;
    const CREATED_AT = "createdate";
    const UPDATED_AT = "modifydate";

    protected $table = "tblexchange";
    protected $primaryKey = "transid";
    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = [
        "transid", "custno", "order_id", "vendor_no", "cylcode_old", "cylcode_new",
        "cylcode_condition", "barcode", "status", "deleted", "createdate",
        "createuser", "modifydate", "modifyuser",
    ];
}
