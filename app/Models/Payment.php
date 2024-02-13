<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    const CREATED_AT = "createdate";
    const UPDATED_AT = "modifydate";

    protected $table = "tblpayment";
    protected $primaryKey = "transid";
    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = [
        "transid", "custno", "payment_mode", "cylcode", 
         "barcode", "amount_due", "amount_paid", "balance",
         "deleted", "createdate",
        "createuser", "modifydate", "modifyuser",
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, "custno", "custno");
    }
}
