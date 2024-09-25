<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Orders extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "orders";
    protected $primaryKey = "id";
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ["id", "customer_id", "location_id", "weight_id", "status", "quantity", "date_acquired"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];
    
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();  // Ensure UUID generation
            }
        });
    }
}
