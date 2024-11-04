<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Orders extends Model
{
    use HasFactory, SoftDeletes;

    const SUCCESS = 'success';
    const CANCELLED = 'canceled';

    protected $table = "orders";
    protected $primaryKey = "id";
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ["id", "customer_id", "location_id", "weight_id", "status", "quantity", "date_acquired", "pickup_location_id", "schedule_date_time","order_number"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    protected $with = ['cylinder_weight','pickup_location','location'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();  // Ensure UUID generation
            }
        });
    }

    public function cylinder_weight()
    {
        return $this->hasMany(CylinderWeights::class, 'id', 'weight_id');
    }

    public function pickup_location()
    {
        return $this->hasMany(Pickup::class, 'id', 'pickup_location_id');
    }

    public function location()
    {
        return $this->hasMany(CustomerLocation::class, 'id', 'location_id');
    }
}
