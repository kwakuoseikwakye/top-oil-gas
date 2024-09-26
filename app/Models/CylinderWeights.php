<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CylinderWeights extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "cylinder_weights";
    protected $primaryKey = "id";
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ["id", "desc", "weight", "amount"];
    protected $with = ['orders'];
    protected $hidden = ["created_at","updated_at", "deleted_at"];

    public function cylinders()
    {
        return $this->hasMany(Cylinder::class, 'weight_id', 'id');
    }

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
