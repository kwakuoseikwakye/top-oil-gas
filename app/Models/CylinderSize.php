<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CylinderSize extends Model
{
    use HasFactory;
    protected $table = "tblcylinder_size";
    protected $primaryKey = "transid";
    public $incrementing = false;
    const CREATED_AT = 'createdate';
    const UPDATED_AT = 'modifydate';

    protected $fillable = [
        'transid',
        'description',
        'createdate',
        'createuser',
        'modifyuser',
        'modifydate',
        'deleted',
    ];
}
