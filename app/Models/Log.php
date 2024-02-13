<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;
    protected $table = "tbllogs";
    protected $primaryKey = "transid";
    public $incrementing = false;
    const CREATED_AT = 'createdate';
    const UPDATED_AT = 'modifydate';

    protected $fillable = [
        'transid',
        'userid',
        'module',
        'action',
        'activity',
        'ipaddress',
        'longitude',
        'latitude',
        'createdate',
        'createuser',
        'modifyuser',
        'modifydate',
        'deleted',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'userid', 'userid');
    }
}
