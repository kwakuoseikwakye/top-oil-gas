<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "customers";
    protected $primaryKey = "id";
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ["id", "fname", "mname", "lname", "gender", "address", "region", "id_type", "id_no", "id_link", "longitude", "latitude", "picture"];
    protected $with = ['orders','locations'];
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

    public function orders()
    {
        return $this->hasMany(Orders::class, 'customer_id', 'id');
    }

    public function locations()
    {
        return $this->hasMany(CustomerLocation::class, 'customer_id', 'id');
    }
}
