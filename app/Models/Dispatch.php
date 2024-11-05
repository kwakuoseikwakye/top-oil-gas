<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Dispatch extends Model
{
    use HasFactory, SoftDeletes;

    const PENDING = 'pending';
    const PENDING_ASSIGNMENT = 'pending_assignment';
    const ASSIGNED = 'assigned';
    const EN_ROUTE = 'en route';
    const DELIVERED = 'delivered';

    protected $table = "dispatch";
    protected $primaryKey = "id";
    public $incrementing = false;
    protected $keyType = "string";

    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    protected $fillable = [
        "id",
        "customer_id",
        "order_number",
        "status",
        "location_id",
        "pickup_location_id",
    ];

    protected $with = ['customer_location','pickup_location'];
    
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();  // Ensure UUID generation
            }
        });
    }

    public function customer_location()
    {
        return $this->belongsTo(CustomerLocation::class, "location_id");
    }

    public function pickup_location()
    {
        return $this->belongsTo(Pickup::class, "pickup_location_id");
    }
}
