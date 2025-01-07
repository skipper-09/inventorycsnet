<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoneOdp extends Model
{
    protected $fillable = [
        'name','zone_id'
    ];
    protected $primaryKey = 'id';
}
