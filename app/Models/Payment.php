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

    const SUCCESS = 'success';
    const FAILED = 'failed';
    const PENDING = 'pending';

    protected $fillable = [
        "transid", "order_id", "payment_mode", "transaction_id", 
         "status", "amount_due", "amount_paid", "balance",
         "deleted", "createdate",
        "createuser", "modifydate", "modifyuser",
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, "custno", "custno");
    }
}
