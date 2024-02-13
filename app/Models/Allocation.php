<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allocation extends Model
{
    use HasFactory;
    const CREATED_AT = "dateallocated";
    const UPDATED_AT = "dateallocated";

    public $incrementing = false;
    protected $primaryKey = 'log';
    protected $table = "allocation";
    public $timestamps = false;

    protected $fillable = [
        'log', 'locationtype', 'currentlocation','cylinder','amount',
        'litres','payment','inital','userlocation','dateallocated','time',
        'active',
    ];
}
