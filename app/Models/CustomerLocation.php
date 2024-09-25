<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
class CustomerLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "customer_locations";
    protected $primaryKey = "id";
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        "name", "customer_id", "phone1", "phone2", "additional_info",
        "longitude", "latitude", "default", "address",
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
}
