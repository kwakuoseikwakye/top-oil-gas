<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "payments";
    protected $primaryKey = "id";
    public $incrementing = false;
    protected $keyType = "string";

    const SUCCESS = 'success';
    const FAILED = 'failed';
    const PENDING = 'pending';

    protected $fillable = [
        "id",
        "order_number",
        "payment_mode",
        "transaction_id",
        "status",
        "customer_id",
        "amount_paid",
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();  // Ensure UUID generation
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, "customer_id", "customer_id");
    }
}
