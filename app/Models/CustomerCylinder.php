<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CustomerCylinder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "customer_cylinders";
    protected $primaryKey = "id";
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        "order_number", "cylcode",
    ];

    protected $hidden = ["updated_at", "deleted_at"];

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
